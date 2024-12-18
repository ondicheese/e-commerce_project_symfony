<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\CollectionRepository;
use App\Repository\PageRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use App\Repository\SlidersRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }
    
    #[Route('/', name: 'app_home')]
    public function index(SettingRepository $settingRepo, SlidersRepository $slidersRepo, CollectionRepository $collectionRepo, PageRepository $pageRepo, CategoryRepository $categoryRepo, Request $req): Response
    {
        $session = $req->getSession();
        $settings = $settingRepo->findAll();
        $sliders = $slidersRepo->findAll();
        $collections = $collectionRepo->findBy(['isMega' => false]);
        $megaCollections = $collectionRepo->findBy(['isMega' => true]);
        $headerPages = $pageRepo->findBy(['isHeader' => true]);
        $footerPages = $pageRepo->findBy(['isFoot' => true]);
        $categories = $categoryRepo->findBy(['isMega' => true]);

        $productsBestSeller = $this->productRepo->findBy(['isBestSeller' => true]);
        $productsNewArrival = $this->productRepo->findBy(['isNewArrival' => true]);
        $productsFeatured = $this->productRepo->findBy(['isFeatured' => true]);
        $productsSpecialOffer = $this->productRepo->findBy(['isSpecialOffer' => true]);

        

        $session->set("setting", $settings[0]);
        $session->set("headerPages", $headerPages);
        $session->set("footerPages", $footerPages);
        $session->set("categories", $categories);
        $session->set("megaCollections", $megaCollections);
        
        //dd([$productsBestSeller, $productsNewArrival, $productsFeatured, $productsSpecialOffer]);
        
        return $this->render('home/index.html.twig', ['sliders' => $sliders, 'collections' => $collections, 'productsNewArrival' => $productsNewArrival, 'productsBestSeller' => $productsBestSeller, 'productsFeatured' => $productsFeatured, 'productsSpecialOffer' => $productsSpecialOffer]);
    }

    #[Route('/product/{slug}', name: 'app_product_by_slug')]
    public function showProduct(string $slug, CategoryRepository $categoryRepo, Request $req)
    {
        $product = $this->productRepo->findOneBy(['slug' => $slug]);

        if (!$product) {
            $this->redirectToRoute('app_error');
        }

        return $this->render('product/show_product_by_slug.html.twig', ['product' => $product]);
    }

    #[Route('/product/get/{productId}', name: 'app_get_product_by_id')]
    public function getProductById(string $productId)
    {
        $product = $this->productRepo->find($productId);

        if (!$product) {
            return $this->json(false);
        }

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'imageUrls' => $product->getImageUrls(),
            'salePrice' => $product->getSalePrice(),
            'regularPrice' => $product->getRegularPrice()
        ]);
    }

    #[Route('/error', name: 'app_error')]
    public function errorPage()
    {
        return $this->render('page/not-found.html.twig');
    }
}