<?php
namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class WishListService
{
    private $session;

    public function __construct(private RequestStack $requestStack, private ProductRepository $productRepo)
    {
        $this->session = $requestStack->getSession();
        $this->productRepo = $productRepo;
    }

    public function getWishList()
    {
        return $this->session->get('WishList', []);
    }

    public function addToWishList(string $productId)
    {
        $wishlist = $this->getWishList();

        if (!isset($wishlist[$productId])) {
            $wishlist[$productId] = 1;
            $this->updateWishList($wishlist);
        }
    }

    public function updateWishList(array $wishlist)
    {
        return $this->session->set('WishList', $wishlist);
    }

    public function removeFromWishList(string $productId)
    {
        $wishlist = $this->getWishList();

        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            $this->updateWishList($wishlist);
        }
    }

    public function emptyWishList()
    {
        $this->updateWishList([]);
    }

    public function getWishListDetails()
    {
        $wishlist = $this->getWishList();
        $result = [];

        foreach ($wishlist as $productId => $quantity) {
            $product = $this->productRepo->find($productId);
            if ($product) {
                $result[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'slug' => $product->getSlug(),
                    'imageUrls' => $product->getImageUrls(),
                    'salePrice' => $product->getSalePrice(),
                    'regularPrice' => $product->getRegularPrice(),
                    'stock' => $product->getStock(),
                ];
            } else {
                unset($wishlist[$productId]);
                $this->updateWishList($wishlist);
            }
        }

        return $result;
    }
}