<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Wishlist;
use App\ItemForm\ItemFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wishlists/{idWishlist}/items')]
#[IsGranted('ROLE_USER')]
final class ItemController extends AbstractController
{
    #[Route('/{idItem}/delete', name: 'delete_item')]
    public function delete(int $idWishlist, int $idItem, EntityManagerInterface $entityManager): Response
    {
        $wishlist=$entityManager->getRepository(Wishlist::class)->find($idWishlist);
        $item=$entityManager->getRepository(Item::class)->find($idItem);
        $entityManager->remove($item);
        $entityManager->flush();    
        return $this->render('wishlist/index.html.twig', [
            'wishlist' => $wishlist,
            'items' => $wishlist->getItems(),
        ]);
    }

    #[Route('/add', name: 'add_item')]
    public function add(int $idWishlist, Request $request, EntityManagerInterface $entityManager): Response
    {
        $wishlist=$entityManager->getRepository(Wishlist::class)->find($idWishlist);
        $item= new Item();
        $form = $this->createForm(ItemFormType::class, $item);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $item->setWishlist($wishlist);
            $item->setHasPurchased(false);
            
            $entityManager->persist($item);

            $entityManager->flush();
            
                return $this->render('wishlist/index.html.twig', [
                    'wishlist' => $wishlist,
                    'items' => $wishlist->getItems(),
                ]);
        }
        return $this->render('item/creatingItem.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form->createView(),
        ]);
    }
}