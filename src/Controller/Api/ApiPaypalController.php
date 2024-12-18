<?php

namespace App\Controller\Api;

use App\Repository\OrderRepository;
use App\Services\PaypalService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiPaypalController extends AbstractController
{
    private $paypalPublicKey;
    private $paypalPrivateKey;
    private $paypalBaseUrl;

    public function __construct(private PaypalService $paypalService, private HttpClientInterface $httpClient)
    {
        $this->paypalPublicKey = $this->paypalService->getPublicKey();
        $this->paypalPrivateKey = $this->paypalService->getPrivateKey();
        $this->paypalBaseUrl = $this->paypalService->getBaseUrl();
    }
    
    #[Route('/api/paypal/orders', name: 'app_api_paypal_orders', methods: ['POST'])]
    public function index(OrderRepository $orderRepo, EntityManagerInterface $entityManager): Response
    {
        $orderId = Request::createFromGlobals()->getContent();
        
        $order = $orderRepo->findOneById($orderId);
        
        if (!$order) {
            return $this->json(['error' => "Order not found !"]);
        }

        $result = $this->createOrder($order);

        if (isset($result['jsonResponse']['id'])) {
            $resultId = $result['jsonResponse']['id'];
            $order->setPaypalClientSecret($resultId);
            $entityManager->persist($order);
            $entityManager->flush();
        }
        
        return $this->json($result['jsonResponse']);
    }

    #[Route('/api/orders/{paypalOrderId}/capture', name: 'app_api_paypal_capture', methods: ['POST'])]
    public function capturePayment($paypalOrderId, OrderRepository $orderRepo, EntityManagerInterface $entityManager): Response
    {
        try {
            $result = $this->captureOrder($paypalOrderId);

            if (isset($result['jsonResponse']['id']) && isset($result['jsonResponse']['status'])) {
                $resultId = $result['jsonResponse']['id'];
                $resultStatus = $result['jsonResponse']['status'];

                if ($resultStatus === "COMPLETED") {
                    $order = $orderRepo->findOneByPaypalClientSecret($resultId);
                    if ($order) {
                        $order->setIsPaid(true);
                        $order->setPaymentMethod('Paypal');
                        $entityManager->persist($order);
                        $entityManager->flush();
                    }
                }
            }

            return $this->json($result['jsonResponse']);
        } catch (Exception $error) {
            error_log("Failed to capture order: " . $error->getMessage());
            return $this->json(['error' => "Failed to capture order."], 500);
        }
    }

    public function generateAccessToken()
    {
        try {
            if (empty($this->paypalPublicKey) || empty($this->paypalPrivateKey)) {
                throw new Exception('MISSING_API_CREDENTIALS');
            }

            $auth = base64_encode($this->paypalPublicKey . ":" . $this->paypalPrivateKey);
    
            $response = $this->httpClient->request(
                'POST',
                $this->paypalBaseUrl . '/v1/oauth2/token',
                [
                    'body' => "grant_type=client_credentials",
                    'headers' => ['Authorization' => 'Basic ' . $auth]
                ]
            );
    
            $data = $response->toArray();
    
            return $data['access_token'];
        }
        catch (\Throwable $th) {
            return null;
        }
    }

    public function createOrder($order)
    {
        $accessToken = $this->generateAccessToken();
        $url = $this->paypalBaseUrl . '/v2/checkout/orders';
        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => $order->getOrderCostTtc()/100,
                    ]
                ]
            ]
        ];

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ],
            'json' => $payload
        ]);

        return $this->handleResponse($response);
    }

    public function captureOrder($orderId)
    {
        $accessToken = $this->generateAccessToken();
        $url = $this->paypalBaseUrl . '/v2/checkout/orders/' . $orderId .'/capture';

        $response = $this->httpClient->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ]
            ]);

            return $this->handleResponse($response);
    }

    public function handleResponse($response)
    {
        try {
            $jsonResponse = json_decode($response->getContent(), true);

            return [
                'jsonResponse' => $jsonResponse,
                'httpStatusCode' => $response->getStatusCode()
            ];
        } catch (\Throwable $th) {
            $errorMessage = (string) $response->getContent();
            throw new Exception($errorMessage);
        }
    }
}
