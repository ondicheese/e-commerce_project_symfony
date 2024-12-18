<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Repository\AddressRepository;
use App\Repository\OrderRepository;
use App\Services\CartService;
use App\Services\PaypalService;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends AbstractController
{
    private $session;
    
    public function __construct(private RequestStack $requestStack, private CartService $cartService, private EntityManagerInterface $entityManager, private OrderRepository $orderRepo)
    {
        $this->session = $requestStack->getSession();
        $this->cartService = $cartService;
    }
    
    #[Route('/checkout', name: 'app_checkout')]
    public function index(AddressRepository $addressRepo, StripeService $stripeService, PaypalService $paypalService): Response
    {
        $cart = $this->cartService->getCartDetails();
        
        if (!count($cart['items'])) {
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();

        if(!$user) {
            $this->session->set('next', 'app_checkout');
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $addresses = $addressRepo->findByUser($user);
        $cart_json = $this->json($cart)->getContent();
        $orderId = "";
        
        $orderId = $this->createOrder($cart);
        
        $stripePublicKey = $stripeService->getPublicKey();
        $paypalPublicKey = $paypalService->getPublicKey();

        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'cart_json' => $cart_json,
            'stripePublicKey' => $stripePublicKey,
            'paypalPublicKey' => $paypalPublicKey,
            'addresses' => $addresses,
            'orderId' => $orderId
        ]);
        
        return new Response('ok');
    }

    #[Route('/stripe/payment/success', name: 'app_stripe_payment_success')]
    public function paymentSuccess(OrderRepository $orderRepo, EntityManagerInterface $entityManager): Response
    {
        $stripeClientSecret = Request::createFromGlobals()->query->get('payment_intent_client_secret');
        $order = $orderRepo->findOneByStripeClientSecret($stripeClientSecret);

        if (!$order) {
            $this->redirectToRoute('app_error');
        }

        $order->setIsPaid(true);
        $order->setStatus('Order validated');
        $order->setPaymentMethod('Stripe');
        $entityManager->persist($order);
        $entityManager->flush();
        $this->cartService->emptyCart();
        return $this->render('payment/index.html.twig');
    }
    #[Route('paypal/payment/success', name: 'app_paypal_payment_success')]
    public function paypalPaymentSuccess(OrderRepository $orderRepo, EntityManagerInterface $entityManager): Response
    {
        $this->cartService->emptyCart();
        
        return $this->render('payment/index.html.twig');
    }

    public function createOrder(array $cart)
    {
        $user = $this->getUser();
        
        $oldOrder = $this->orderRepo->findOneBy([
            "client_name" => $user->getFullName(),
            "order_cost" => $cart['sub_total_ht']*100,
            "taxe" => $cart['tax']*100,
            "order_cost_ttc" => $cart['sub_total_with_carrier']*100,
            "carrier_name" => $cart['carrier']['name'],
            "carrier_price" => $cart['carrier']['price']*100,
            "carrier_id" => $cart['carrier']['id'],
            "quantity" => $cart['cart_count'],
            "isPaid" => false,
        ]);
        
        if ($oldOrder) {
            return $oldOrder->getId();
        }
        
        $order = new Order();
        $order->setClientName($user->getFullName())
              ->setUserId($user->getId())
              ->setBillingAddress("")
              ->setShippingAddress("")
              ->setOrderCost($cart['sub_total_ht']*100)
              ->setTaxe($cart['tax']*100)
              ->setOrderCostTtc($cart['sub_total_with_carrier']*100)
              ->setCarrierName($cart['carrier']['name'])
              ->setCarrierPrice($cart['carrier']['price']*100)
              ->setCarrierId($cart['carrier']['id'])
              ->setQuantity($cart['cart_count'])
              ->setStatus('Awaiting payment')
              ->setIsPaid(false)
        ;

        $this->entityManager->persist($order);

        foreach($cart['items'] as $item) {
            $product = $item['product'];
            $orderDetails = new OrderDetails();
            $orderDetails->setProductName($product['name'])
                        ->setProductSalePrice($product['salePrice'])
                        ->setProductRegularPrice($product['regularPrice'])
                        ->setProductDescription($product['description'])
                        ->setQuantity($item['quantity'])
                        ->setSubtotal($item['sub_total'])
                        ->setTaxe($item['tax']*100)
                        ->setMyOrder($order)
            ;
            $this->entityManager->persist($orderDetails);
        }
        
        $this->entityManager->flush();

        return $order->getId();
    }
}
