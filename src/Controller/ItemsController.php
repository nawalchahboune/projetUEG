<?php

namespace App\Controller;

use App\Entity\Wishlist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


final class ItemsController extends AbstractController
{
    
    #[Route('/wishlists/{id}/items', name: 'app_wishlist_show')]
    public function show(Wishlist $wishlist): Response
    {
        // Check if user is owner or collaborator
        if ($wishlist->getOwner() !== $this->getUser() && !$wishlist->getCollaborators()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette liste');
        }
        
        return $this->render('wishlist/index.html.twig', [
            'wishlist' => $wishlist,
            'items' => $wishlist->getItems(),
        ]);
    }

}
