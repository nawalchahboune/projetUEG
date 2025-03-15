<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Form\WishlistType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/myWishlists')]
#[IsGranted('ROLE_USER')]
class WishlistsController extends AbstractController
{
    #[Route('', name: 'app_wishlists_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        $ownedWishlists = $entityManager->getRepository(Wishlist::class)->findBy(['owner' => $user]);
        $collaborativeWishlists = $user->getCollaborativeWishlists();
        
        return $this->render('wishlists/index.html.twig', [
            'ownedWishlists' => $ownedWishlists,
            'collaborativeWishlists' => $collaborativeWishlists,
        ]);
    }

    #[Route('/create', name: 'app_wishlists_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $wishlist = new Wishlist();
        $wishlist->setOwner($this->getUser());
        
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($wishlist);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre liste de souhaits a été créée avec succès');
            return $this->redirectToRoute('app_wishlists_index');
        }
        
        return $this->render('wishlists/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_wishlists_edit')]
    public function edit(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        // Check if user is the owner
        if ($wishlist->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette liste');
        }
        
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre liste de souhaits a été modifiée avec succès');
            return $this->redirectToRoute('app_wishlists_index');
        }
        
        return $this->render('wishlists/edit.html.twig', [
            'form' => $form,
            'wishlist' => $wishlist,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_wishlists_delete', methods: ['POST'])]
    public function delete(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
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
        
        return $this->redirectToRoute('app_wishlists_index');
    }

    #[Route('/{id}/getUrl', name: 'app_wishlists_get_url')]
    public function getUrl(Wishlist $wishlist, SluggerInterface $slugger): Response
    {
        // Check if user is owner or collaborator
        if ($wishlist->getOwner() !== $this->getUser() && !$wishlist->getCollaborators()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette liste');
        }
        
        $slug = $slugger->slug($wishlist->getName() . '-' . $wishlist->getId());
        $url = $this->generateUrl('app_wishlist_show', ['id' => $wishlist->getId(), 'slug' => $slug], true);
        
        return $this->render('wishlists/get_url.html.twig', [
            'wishlist' => $wishlist,
            'url' => $url,
        ]);
    }
    
    #[Route('/store', name: 'app_wishlists_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $wishlist = new Wishlist();
        $wishlist->setName($data['name']);
        $wishlist->setOwner($this->getUser());
        
        if (isset($data['deadline'])) {
            $wishlist->setDeadline(new \DateTime($data['deadline']));
        }
        
        $entityManager->persist($wishlist);
        $entityManager->flush();
        
        return $this->json([
            'success' => true,
            'id' => $wishlist->getId(),
            'message' => 'Liste de souhaits créée avec succès'
        ]);
    }
}