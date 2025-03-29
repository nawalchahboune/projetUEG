<?php

namespace App\Controller;

use App\Entity\Wishlist;
use App\Entity\Invitation;
use App\ItemForm\WishlistType;
use App\Repository\UserRepository;
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
use Symfony\Component\Form\FormError;  

#[Route('/myWishlists')]
#[IsGranted('ROLE_USER')]
class WishlistsController extends AbstractController
{
    #[Route('/', name: 'app_wishlists_index')]
    public function index(WishlistRepository $wishlistRepository, UserRepository $userRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Si aucun utilisateur n'est connecté, on affiche une page basique ou on redirige
        if (!$user) {
            return $this->render('security/login.html.twig');
        }
        $users = $userRepository->findAll();
        
        // Récupérer toutes les wishlists dont l'utilisateur est propriétaire
        $wishlists = $wishlistRepository->findBy(['owner' => $user]);
        $wishlistsIamCollaborator = $wishlistRepository->findCollaboratorWishlists($user);
        
        return $this->render('wishlists/index.html.twig', [
            'wishlists' => $wishlists,
            'wishlistsIamCollaborator' => $wishlistsIamCollaborator,
            'users' => $users
        ]);
    }

    #[Route('/create', name: 'app_wishlists_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $wishlist = new Wishlist();
        $wishlist->setOwner($this->getUser());
        $wishlist->setPublicToken();
        $wishlist->setCollaborationToken();
        
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Validate deadline: must be strictly greater than today if set
            if ($wishlist->getDeadline() !== null && $wishlist->getDeadline() <= new \DateTime('today')) {
                $form->get('deadline')->addError(new FormError('La date limite doit être supérieure à la date du jour.'));
            } else {
                // Process collaborators from the hidden input field
                $collaboratorsInput = $request->request->get('collaborators_hidden', '');
                if (!empty($collaboratorsInput)) {
                    $usernames = array_filter(array_map('trim', explode(',', $collaboratorsInput)));
                    foreach ($usernames as $username) {
                        if (!empty($username)) {
                            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
                            if ($user) {
                                $wishlist->addCollaborator($user);
                            } else {
                                // Optional: add flash message or log error if user not found
                                $this->addFlash('error', "L'utilisateur '{$username}' n'a pas été trouvé.");
                            }
                        }
                    }
                }
                $entityManager->persist($wishlist);
                $entityManager->flush();
                return $this->redirectToRoute('app_wishlists_index');
            }
        }
        
        return $this->render('wishlists/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}/edit', name: 'app_wishlists_edit')]
    public function edit(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        // Allow access if the user is either the owner or is already a collaborator
        if ($wishlist->getOwner() !== $this->getUser() && !$wishlist->getCollaborators()->contains($this->getUser())) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette liste');
        }

        // Pass the option to include collaborators field in the form.
        $form = $this->createForm(WishlistType::class, $wishlist, [
            'include_collaborators' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Process collaborators from the hidden field
            $collaboratorsInput = $request->request->get('collaborators_hidden', '');
            if (!empty($collaboratorsInput)) {
                $usernames = array_filter(array_map('trim', explode(',', $collaboratorsInput)));
                // Remove collaborators that are no longer in the hidden field
                foreach ($wishlist->getCollaborators() as $collaborator) {
                    if (!in_array($collaborator->getUsername(), $usernames)) {
                        $wishlist->removeCollaborator($collaborator);
                    }
                }
                // Add new collaborators not already in the collection
                foreach ($usernames as $username) {
                    $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
                    if ($user && !$wishlist->getCollaborators()->contains($user)) {
                        $wishlist->addCollaborator($user);
                    }
                }
            } else {
                // If the hidden field is empty, you might want to remove all collaborators:
                foreach ($wishlist->getCollaborators() as $collaborator) {
                    $wishlist->removeCollaborator($collaborator);
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_wishlists_index');
        }

        return $this->render('wishlists/edit.html.twig', [
            'form' => $form->createView(),
            'wishlist' => $wishlist,
        ]);
    }


    #[Route('/{id}/delete', name: 'app_wishlists_delete', methods: ['POST'])]
    public function delete(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        if ($wishlist->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette liste');
        }
        
        if ($this->isCsrfTokenValid('delete' . $wishlist->getId(), $request->request->get('_token'))) {
            $entityManager->remove($wishlist);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('app_wishlists_index');
    }

    #[Route('/{id}/getUrl', name: 'app_wishlists_get_url')]
    public function getUrl(Wishlist $wishlist, SluggerInterface $slugger): Response
    {
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
            $deadline = new \DateTime($data['deadline']);
            if ($deadline <= new \DateTime('today')) {
                return $this->json([
                    'success' => false,
                    'message' => 'La date limite doit être supérieure à la date du jour.'
                ], Response::HTTP_BAD_REQUEST);
            }
            $wishlist->setDeadline($deadline);
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
        // Vérifier que l'utilisateur connecté est bien le propriétaire
        if ($wishlist->getOwner() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas l\'autorisation de partager cette liste.');
            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        // Récupérer les noms d'utilisateur soumis
        // On s'attend à recevoir un tableau de strings (les usernames)
        $usernames = $request->request->get('usernames', []);
        if (!is_array($usernames)) {
            // Si c'est une seule chaîne, on le transforme en tableau
            $usernames = [$usernames];
        }

        $invalidUsernames = [];
        foreach ($usernames as $username) {
            $username = trim($username);
            if (empty($username)) {
                continue;
            }
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);
            if (!$user) {
                $invalidUsernames[] = $username;
            } else {
                // Ajouter une invitation pour l'utilisateur trouvé
                $user->addInvitation(new Invitation($wishlist, $this->getUser(), $user));
            }
        }

        if (count($invalidUsernames) > 0) {
            $this->addFlash('error', 'Les noms d\'utilisateur suivants n\'existent pas : ' . implode(', ', $invalidUsernames));
            return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
        }

        $entityManager->flush();

        $this->addFlash('success', 'La liste de souhaits a été partagée avec succès.');
        return $this->redirectToRoute('app_wishlist_show', ['id' => $wishlist->getId()]);
    }

}
