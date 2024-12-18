<?php
namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CompareService
{
    private $session;

    public function __construct(private RequestStack $requestStack, private ProductRepository $productRepo)
    {
        $this->session = $requestStack->getSession();
        $this->productRepo = $productRepo;
    }

    public function getCompare()
    {
        return $this->session->get('Compare', []);
    }

    public function addToCompare(string $productId)
    {
        $compare = $this->getCompare();

        if (!isset($compare[$productId])) {
            $compare[$productId] = 1;
            $this->updateCompare($compare);
        }
    }

    public function updateCompare(array $compare)
    {
        return $this->session->set('Compare', $compare);
    }

    public function removeFromCompare(string $productId)
    {
        $compare = $this->getCompare();

        if (isset($compare[$productId])) {
            unset($compare[$productId]);
            $this->updateCompare($compare);
        }
    }

    public function emptyCompare()
    {
        $this->updateCompare([]);
    }

    public function getCompareDetails()
    {
        $compare = $this->getCompare();
        $result = [];

        foreach ($compare as $productId => $quantity) {
            $product = $this->productRepo->find($productId);
            if ($product) {
                $result[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'slug' => $product->getSlug(),
                    'imageUrls' => $product->getImageUrls(),
                    'salePrice' => $product->getSalePrice(),
                    'regularPrice' => $product->getRegularPrice()
                ];
            } else {
                unset($compare[$productId]);
                $this->updateCompare($compare);
            }
        }

        return $result;
    }
}