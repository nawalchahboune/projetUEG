<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Entity\Item;
use App\Form\ItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/myWishlist')]
#[IsGranted('ROLE_USER')]
class WishlistController extends AbstractController
{
    #[Route('/{id}', name: 'app_wishlist_show')]
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
    /*

    #[Route('/{id}/insertItem', name: 'app_wishlist_insert_item')]
    public function insertItem(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        // Check if user is owner or collaborator
        if ($wishlist->getOwner() !== $this->getUser() && !$wishlist->getCollaborators()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette liste');
        }
        
        $item = new Item();
        $item->setWishlist($wishlist);
        $item->setHasPurchased(false);
        
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($item);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'élément a été ajouté à votre liste');
            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }
        
        return $this->render('wishlist/insert_item.html.twig', [
            'form' => $form,
            'wishlist' => $wishlist,
        ]);
    }

    #[Route('/{wishlistId}/modifyItem/{itemId}', name: 'app_wishlist_modify_item')]
    public function modifyItem(Request $request, int $wishlistId, int $itemId, EntityManagerInterface $entityManager): Response
    {
        $wishlist = $entityManager->getRepository(Wishlist::class)->find($wishlistId);
        $item = $entityManager->getRepository(Item::class)->find($itemId);
        
        if (!$wishlist || !$item || $item->getWishlist() !== $wishlist) {
            throw $this->createNotFoundException('L\'élément ou la liste de souhaits n\'existe pas');
        }
        
        // Check if user is owner or collaborator
        if ($wishlist->getOwner() !== $this->getUser() && !$wishlist->getCollaborators()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette liste');
        }
        
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'élément a été modifié avec succès');
            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlistId]);
        }
        
        return $this->render('wishlist/modify_item.html.twig', [
            'form' => $form,
            'wishlist' => $wishlist,
            'item' => $item,
        ]);
    }

    #[Route('/{wishlistId}/deleteItem/{itemId}', name: 'app_wishlist_delete_item', methods: ['POST'])]
    public function deleteItem(Request $request, int $wishlistId, int $itemId, EntityManagerInterface $entityManager): Response
    {
        $wishlist = $entityManager->getRepository(Wishlist::class)->find($wishlistId);
        $item = $entityManager->getRepository(Item::class)->find($itemId);
        
        if (!$wishlist || !$item || $item->getWishlist() !== $wishlist) {
            throw $this->createNotFoundException('L\'élément ou la liste de souhaits n\'existe pas');
        }
        
        // Check if user is owner or collaborator
        if ($wishlist->getOwner() !== $this->getUser() && !$wishlist->getCollaborators()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette liste');
        }
        
        if ($this->isCsrfTokenValid('delete'.$itemId, $request->request->get('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'élément a été supprimé');
        }
        
        return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlistId]);
    }*/

    #[Route('/{id}/deleteWishlist', name: 'app_wishlist_delete_wishlist', methods: ['POST'])]
    public function deleteWishlist(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        // Check if user is the owner
        if ($wishlist->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette liste');
        }
        
        if ($this->isCsrfTokenValid('delete'.$wishlist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wishlist);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre liste de souhaits a été supprimée');
        }
        
        return $this->redirectToRoute('app_home');
    }

    #[Route('/{wishlistId}/goToOfficialWebsite/{itemId}', name: 'app_wishlist_go_to_official_website')]
    public function goToOfficialWebsite(int $wishlistId, int $itemId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $wishlist = $entityManager->getRepository(Wishlist::class)->find($wishlistId);
        $item = $entityManager->getRepository(Item::class)->find($itemId);
        
        if (!$wishlist || !$item || $item->getWishlist() !== $wishlist) {
            throw $this->createNotFoundException('L\'élément ou la liste de souhaits n\'existe pas');
        }
        
        // Check if user is owner or collaborator
        if ($wishlist->getOwner() !== $this->getUser() && !$wishlist->getCollaborators()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette liste');
        }
        
        $url = $item->getUrl();
        
        if (!$url) {
            $this->addFlash('error', 'Cet élément n\'a pas d\'URL définie');
            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlistId]);
        }
        
        // Ensure URL has protocol
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        
        return new RedirectResponse($url);
    }
    #[Route('/wishlist/{id}/share', name: 'wishlist_share')]
    public function share(Wishlist $wishlist): Response
    {
        // Check if current user is owner
        if ($wishlist->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas l\'autorisation de partager cette liste.');
            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }
        
        // Generate URLs
        $collaborationUrl = $this->generateUrl(
            'wishlist_collaborate', 
            ['token' => $wishlist->getCollaborationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        
        $publicUrl = $this->generateUrl(
            'wishlist_public',
            ['token' => $wishlist->getPublicToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        
        return $this->render('wishlist/share.html.twig', [
            'wishlist' => $wishlist,
            'collaborationUrl' => $collaborationUrl,
            'publicUrl' => $publicUrl
        ]);
    }
}