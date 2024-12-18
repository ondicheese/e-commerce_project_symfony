<?php

namespace App\Controller;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/page/{slug}', name: 'app_page')]
    public function index(string $slug, PageRepository $pagesRepo): Response
    {
        $page = $pagesRepo->findOneBy(['slug' => $slug]);

        if (!$page) {
            return $this->render('page/not-found.html.twig');
        }
        
        return $this->render('page/index.html.twig', ['page' => $page]);
    }
}
