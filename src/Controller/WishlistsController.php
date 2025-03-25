<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Entity\Invitation;
use App\Controller\WishlistType;
use App\Entity\User;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\Collections\ArrayCollection;

#[Route('/myWishlists')]
#[IsGranted('ROLE_USER')]
class WishlistsController extends AbstractController
{
    #[Route('/', name: 'app_wishlists_index')]
    public function index(WishlistRepository $wishlistRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Si aucun utilisateur n'est connecté, on affiche une page basique ou on redirige
        if (!$user) {
            return $this->render('security/login.html.twig');
            // Ou rediriger vers la page de login
            // return $this->redirectToRoute('app_login');
        }
        
        // Récupérer toutes les wishlists dont l'utilisateur est propriétaire
        $wishlists = $wishlistRepository->findBy(['owner' => $user]);
        
        return $this->render('wishlists/index.html.twig', [
            'wishlists' => $wishlists,

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

    #[Route('/{id}/share', name: 'wishlist_share', methods: ['POST'])]
    public function shareWishlist(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        // Check if current user is owner
        if ($wishlist->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas l\'autorisation de partager cette liste.');
            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        $userIds = $request->request->get('user_ids');
        if (is_array($userIds)) {
            $users = new ArrayCollection();

            foreach ($userIds as $userId) {                
                $user = $entityManager->getRepository(User::class)->find($userId);                
                if ($user) {                    
                    $user->addInvitation(new Invitation($wishlist, $this->getUser(), $user));
                }
            }
        }
        $entityManager->flush();



        $this->addFlash('success', 'La liste de souhaits a été partagée avec succès.');
        return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
    }
}
