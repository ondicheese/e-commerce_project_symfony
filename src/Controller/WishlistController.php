<?php

namespace App\Controller;

use App\Services\WishListService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WishlistController extends AbstractController
{
    public function __construct(private WishListService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }
    
    #[Route('/wishlist', name: 'app_wishlist')]
    public function index(): Response
    {
        $wishlist = $this->wishlistService->getWishListDetails();
        $wishlist_json = json_encode($wishlist);
//dd($wishlist_json);
        return $this->render('wishlist/index.html.twig', [
            'wishlist' => $wishlist,
            'wishlist_json' => $wishlist_json
        ]);
    }

    #[Route('/wishlist/add/{productId}', name: 'app_add_to_wishlist')]
    public function addToWishList(string $productId): Response
    {
        $this->wishlistService->addToWishList($productId);
        $wishlist = $this->wishlistService->getWishListDetails();
        
        return $this->json($wishlist);
        //return $this->redirectToRoute("app_wishlist");
    }

    #[Route('/wishlist/remove/{productId}', name: 'app_remove_from_wishlist')]
    public function removeFromWishList(string $productId): Response
    {
        $this->wishlistService->removeFromWishList($productId);
        $wishlist = $this->wishlistService->getWishListDetails();
        
        //return $this->redirectToRoute("app_wishlist");
        return $this->json($wishlist);
    }
    #[Route('/wishlist/get', name: 'app_get_wishlist')]
    public function getWishList()
    {
        $wishlist = $this->wishlistService->getWishListDetails();
        return $this->json($wishlist);
    }
}
