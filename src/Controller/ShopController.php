<?php

namespace App\Controller;

use Pagerfanta\Pagerfanta;
use App\Services\CartService;
use App\Repository\CarrierRepository;
use App\Repository\ProductRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShopController extends AbstractController
{
    #[Route('/shop-list', name: 'app_shop_list')]
    public function index(ProductRepository $productRepo, CartService $cartService, CarrierRepository $carrierRepo, Request $request): Response
    {
        $products = $productRepo->findAll();
        $cart = $cartService->getCartDetails();
        $cart = json_encode($cart);

        $carriersTab = $carrierRepo->findAll();
        $carriers = [];

        foreach($carriersTab as $key => $carrier) {
            $carriers[$key] = [
                'id' => $carrier->getId(),
                'name' => $carrier->getName(),
                'price' => $carrier->getPrice() / 100,
                'description' => $carrier->getDescription()
            ];
        }

        $carriers = json_encode($carriers);

        $pageNb = $request->query->get('page', 1);
        $queryBuilder = $productRepo->showProductsQueryBuilder();
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = Pagerfanta::createForCurrentPageWithMaxPerPage($adapter, $pageNb, 9);
        
        return $this->render('shop/index.html.twig', [
            'products' => $products,
            'cart' => $cart,
            'carriers' => $carriers,
            'pager' => $pagerfanta,
            'pageNb' => $pageNb
        ]);
    }
}
