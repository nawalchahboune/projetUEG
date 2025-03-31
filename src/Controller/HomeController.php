<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ItemRepository;


class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
    
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request, ItemRepository $itemRepository): Response
    {
        $query = $request->query->get('q', '');
        $results = [];
        
        if ($query) {
            $results = $itemRepository->searchItems($query);
        }
        
        return $this->render('home/search.html.twig', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}