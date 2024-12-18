<?php

namespace App\Controller\Api;

use App\Repository\OrderRepository;
use Error;
use Stripe\StripeClient;
use App\Services\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ApiStripeController extends AbstractController
{
    #[Route('/api/stripe/payment-intent/{orderId}', name: 'app_api_stripe_payment_intent', methods: ['POST'])]
    public function index(string $orderId, StripeService $stripeService, OrderRepository $orderRepo, EntityManagerInterface $entityManager): Response
    {
        try {
            $stripeSecretKey = $stripeService->getPrivateKey();
            $stripe = new StripeClient($stripeSecretKey);
            
            $order = $orderRepo->find($orderId);

            if (!$order) {
                return $this->json(['error' => 'Order not found !']);
            }

            // Create a PaymentIntent with amount and currency
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $order->getOrderCostTtc(),
                'currency' => 'eur',
                // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
                // [DEV]: For demo purposes only, you should avoid exposing the PaymentIntent ID in the client-side code.
                'dpmCheckerLink' => "https://dashboard.stripe.com/settings/payment_methods/review?transaction_id={$paymentIntent->id}",
            ];

            $order->setStripeClientSecret($paymentIntent->client_secret);
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->json($output);
        } catch (\Throwable $th) {
            return $this->json(['error' => $th->getMessage()]);
        }
        
    }

    
}
