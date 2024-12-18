<?php

namespace App\Controller\Api;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiOrderController extends AbstractController
{
    #[Route('/api/order', name: 'app_api_order', methods: ['POST'])]
    public function update(OrderRepository $orderRepo, EntityManagerInterface $entityManager): Response
    {
        $data = Request::createFromGlobals()->request;
        $orderId = $data->get('orderId');
        $order = $orderRepo->find($orderId);

        if (!$order) {
            return $this->redirectToRoute("/checkout");
        }

        $order->setBillingAddress($data->get('billing_address'));
        $order->setShippingAddress($data->get('shipping_address'));

        $entityManager->persist($order);
        $entityManager->flush();
        
        return $this->json($data);
    }
}
