<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Entity\Item;
use App\Entity\User; // Add missing User entity import
use App\Form\ItemType;
use App\ItemForm\WishlistType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/myWishlist')]
class WishlistController extends AbstractController
{
    // Move specific routes BEFORE the generic {id} route
    #[IsGranted('ROLE_USER')]
    #[Route('/create', name: 'app_wishlist_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $wishlist = new Wishlist();
        $wishlist->setOwner($this->getUser());
        $wishlist->setPublicToken();
        $wishlist->setCollaborationToken();

        // Création du formulaire (le reste des champs est géré par votre FormType)
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le champ caché qui contient les usernames
            $collaboratorsInput = $request->request->get('collaborators_hidden', '');
            if (!empty($collaboratorsInput)) {
                $usernames = array_map('trim', explode(',', $collaboratorsInput));
                foreach ($usernames as $username) {
                    if (!empty($username)) {
                        $user = $entityManager->getRepository(\App\Entity\User::class)
                                            ->findOneBy(['username' => $username]);
                        if ($user) {
                            // ajouter directement le collaborateur à la wishlist
                            $wishlist->addCollaborator($user);
                            $entityManager->flush();
                            // Créer une invitation, selon votre logique
                        }
                    }
                }
            }

            $entityManager->persist($wishlist);
            $entityManager->flush();

            $this->addFlash('success', 'La liste de souhaits a été créée avec succès.');
            return $this->redirectToRoute('app_wishlists_index');
        }

        return $this->render('wishlist/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/token', name: 'app_wishlist_public')]
    public function public(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->query->get('token');
        $wishlist = $entityManager->getRepository(Wishlist::class)->findOneBy(['publicToken' => $token]);
        
        if (!$wishlist) {
            throw $this->createNotFoundException('La liste de souhaits n\'existe pas');
        }
        $user= $this->getUser();
        
        return $this->render('wishlist/index.html.twig', [
            'wishlist' => $wishlist,
            'items' => $wishlist->getItems(),
            'user' => $user,
            'aim'=>'toBuy'
        ]);
    }

    #[Route('/check-username', name: 'app_check_username', methods: ['POST'])]
    public function checkUsername(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $username = $request->request->get('username');
        if (!$username) {
            return $this->json(['success' => false, 'message' => 'Aucun username reçu'], 400);
        }
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($user) {
            return $this->json(['exists' => true, 'username' => $username]);
        } else {
            return $this->json(['exists' => false, 'message' => 'Utilisateur non trouvé'], 404);
        }
    }

    // Then place the generic {id} route AFTER specific routes
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_wishlist_show', requirements: ['id' => '\d+'])]
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

    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/deleteWishlist', name: 'app_wishlist_delete_wishlist', methods: ['POST'])]
    public function deleteWishlist(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        // Check if user is the owner
        if ($wishlist->getOwner() !== $this->getUser() && $wishlist->getCollaborators()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette liste');
        }
        
        if ($this->isCsrfTokenValid('delete'.$wishlist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wishlist);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre liste de souhaits a été supprimée');
        }
        
        return $this->redirectToRoute('app_wishlists_index');
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

    #[IsGranted('ROLE_USER')]
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
            'ViewUserWishlist',
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