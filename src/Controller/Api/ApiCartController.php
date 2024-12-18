<?php

namespace App\Controller\Api;

use App\Repository\CarrierRepository;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiCartController extends AbstractController
{
    #[Route('/api/cart/update/carrier/{id}', name: 'app_api_cart', methods: ['GET'])]
    public function index($id, CarrierRepository $carrierRepo, CartService $cartService): Response
    {
        $carrier = $carrierRepo->find($id);
        
        if (!$carrier) {
            return $this->json([
                'isSuccess' => false,
                'message' => 'Carrier not found !'
            ]);
        }
        
        $cartService->update('carrier', [
            'id' => $carrier->getId(),
            'name' => $carrier->getName(),
            'price' => $carrier->getPrice() / 100,
            'description' => $carrier->getDescription()
        ]);
        
        return $this->json([
            'isSuccess' => true,
            'data' => $cartService->getCartDetails()
        ]);
    }
}
