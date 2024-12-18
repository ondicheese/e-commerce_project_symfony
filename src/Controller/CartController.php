<?php

namespace App\Controller;

use App\Repository\CarrierRepository;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(private CartService $cartService, private CarrierRepository $carrierRepo)
    {
        $this->cartService = $cartService;
    }
    
    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $cart = $this->cartService->getCartDetails();
        $carriers = $this->carrierRepo->findAll();
        $carriersTab = [];

        foreach($carriers as $key => $carrier) {
            $carriersTab[$key] = [
                'id' => $carrier->getId(),
                'name' => $carrier->getName(),
                'price' => $carrier->getPrice() / 100,
                'description' => $carrier->getDescription()
            ];
        }

        $cart_json = json_encode($cart);
        $carriers_json = json_encode($carriersTab);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'cart_json' => $cart_json,
            'carriers_json' => $carriers_json,
            'carriers' => $carriers
        ]);
    }

    #[Route('/cart/add/{productId}/{count}', name: 'app_add_to_cart')]
    public function addToCart(string $productId, $count = 1)
    {
        $this->cartService->addToCart($productId, $count);
        $cart = $this->cartService->getCartDetails();
        
        return $this->json($cart);
    }

    #[Route('/cart/remove/{productId}/{count}', name: 'app_remove_from_cart')]
    public function removeFromCart(string $productId, $count = 1)
    {
        $this->cartService->removeFromCart($productId, $count);
        $cart = $this->cartService->getCartDetails();
        
        return $this->json($cart);
    }

    #[Route('/cart/get', name: 'app_get_cart')]
    public function getCart()
    {
        $cart = $this->cartService->getCartDetails();
        return $this->json($cart);
    }

    #[Route('/cart/carrier', name: 'app_update_cart_carrier', methods: ['POST'])]
    public function updateCartCarrier()
    {
        $carrierId = Request::createFromGlobals()->request->get('carrierId');
        
        $carrier = $this->carrierRepo->find($carrierId);

        if (!$carrier) {
            return $this->redirectToRoute('app_home');
        }

        $this->cartService->update('carrier', [
            'id' => $carrier->getId(),
            'name' => $carrier->getName(),
            'price' => $carrier->getPrice() / 100,
            'description' => $carrier->getDescription()
        ]);

        return $this->redirectToRoute('app_cart');
    }
}
