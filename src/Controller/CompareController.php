<?php

namespace App\Controller;

use App\Services\CompareService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompareController extends AbstractController
{
    public function __construct(private CompareService $compareService)
    {
        $this->compareService = $compareService;
    }
    
    #[Route('/compare', name: 'app_compare')]
    public function index(): Response
    {
        $compare = $this->compareService->getCompareDetails();
        $compare_json = json_encode($compare);
//dd($compare_json);
        return $this->render('compare/index.html.twig', [
            'compare' => $compare,
            'compare_json' => $compare_json
        ]);
    }

    #[Route('/compare/add/{productId}', name: 'app_add_to_compare')]
    public function addToCompare(string $productId): Response
    {
        $this->compareService->addToCompare($productId);
        $compare = $this->compareService->getCompareDetails();
        
        return $this->json($compare);
        //return $this->redirectToRoute("app_compare");
    }

    #[Route('/compare/remove/{productId}', name: 'app_remove_from_compare')]
    public function removeFromCompare(string $productId): Response
    {
        $this->compareService->removeFromCompare($productId);
        $compare = $this->compareService->getCompareDetails();
        
        return $this->json($compare);
    }
    #[Route('/compare/get', name: 'app_get_compare')]
    public function getCompare()
    {
        $compare = $this->compareService->getCompareDetails();
        return $this->json($compare);
    }
}
