<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    
    #[Route('/search', name: 'app_gift_search')]
    public function search(Request $request): Response
    {
        $query = $request->query->get('q', '');
        
        // In a real application, you would search your database here
        // For now, we'll just pass the query to the template
        
        return $this->render('home/search_results.html.twig', [
            'query' => $query,
            'results' => [], // This would be filled with actual search results
        ]);
    }
}