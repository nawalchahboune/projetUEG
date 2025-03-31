<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Wishlist;
use App\ItemForm\ItemFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wishlists/{idWishlist}/items')]
#[IsGranted('ROLE_USER')]
final class ItemController extends AbstractController
{
    #[Route('/{idItem}/delete', name: 'delete_item', methods: ['POST'])]
    public function delete(int $idWishlist, int $idItem, EntityManagerInterface $entityManager): Response
    {
        $wishlist = $entityManager->getRepository(Wishlist::class)->find($idWishlist);
        $item = $entityManager->getRepository(Item::class)->find($idItem);
        
        // Vérification des permissions via le voter
        $this->denyAccessUnlessGranted('DELETE', $item);
        
        if ($wishlist->getOwner() == $this->getUser() || $wishlist->getCollaborators()->contains($this->getUser())) {
            // S'il y a une preuve associée, on la supprime et on effectue un flush
            if ($item->getProof()) {
                $proof = $item->getProof();
                $item->setProof(null); 
                $entityManager->remove($proof);
                $entityManager->flush();
            }
            // Supprime l'item
            $entityManager->remove($item);
            $entityManager->flush();
        }
        
        // Redirige vers la wishlist
        return $this->redirectToRoute('app_wishlist_show', ['id' => $idWishlist]);
    }


    #[Route('/add', name: 'add_item')]
    public function add(int $idWishlist, Request $request, EntityManagerInterface $entityManager): Response
    {
        $wishlist = $entityManager->getRepository(Wishlist::class)->find($idWishlist);
        $item = new Item();
        $this->denyAccessUnlessGranted('ADD', $item);
        $form = $this->createForm(ItemFormType::class, $item);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $item->setWishlist($wishlist);
            $item->setHasPurchased(false);
            if ($wishlist->getOwner() == $this->getUser() || $wishlist->getCollaborators()->contains($this->getUser())) {
                $entityManager->persist($item);
                $entityManager->flush();
                return $this->redirectToRoute('app_wishlist_show', [
                    'id' => $idWishlist,
                ]);
            }
        }
        
        return $this->render('item/creatingItem.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{idItem}/edit', name: 'edit_item')]
    public function edit(int $idWishlist, int $idItem, Request $request, EntityManagerInterface $entityManager): Response
    {
        $wishlist = $entityManager->getRepository(Wishlist::class)->find($idWishlist);
        $item = $entityManager->getRepository(Item::class)->find($idItem);
        $this->denyAccessUnlessGranted('EDIT', $item);

        // Vérifie que l'item existe et qu'il appartient à la wishlist
        if (!$item || $item->getWishlist()->getId() !== $wishlist->getId()) {
            throw $this->createNotFoundException('Item not found in this wishlist');
        }
        
        $form = $this->createForm(ItemFormType::class, $item);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($wishlist->getOwner() == $this->getUser() || $wishlist->getCollaborators()->contains($this->getUser())) {
                $entityManager->persist($item);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_wishlist_show', [
                'id' => $idWishlist,
            ]);
        }
        
        return $this->render('item/editItem.html.twig', [
            'wishlist' => $wishlist,
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }
}
