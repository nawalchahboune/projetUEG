<?php
// src/Controller/ApiController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wishlist;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    /**
     * Recherche des utilisateurs par nom d'utilisateur
     */
    #[Route('/users/search', name: 'users_search', methods: ['GET'])]
    public function searchUsers(Request $request, UserRepository $userRepository): JsonResponse
    {
        $query = $request->query->get('query', '');
        
        if (strlen($query) < 2) {
            return $this->json([]);
        }
        
        // Rechercher les utilisateurs dont le nom d'utilisateur commence par la requête
        $users = $userRepository->findByUsername($query);
        
        // Formater les données pour la réponse JSON
        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'avatarPath' => 'images/avatars/default.png'
            ];
        }
        
        return $this->json($formattedUsers);
    }
    
    /**
     * Obtenir des suggestions d'utilisateurs (5 premiers utilisateurs par exemple)
     */
    #[Route('/users/suggestions', name: 'users_suggestions', methods: ['GET'])]
    public function getUserSuggestions(UserRepository $userRepository): JsonResponse
    {
        // Obtenir l'utilisateur actuel
        $currentUser = $this->getUser();
        
        // Obtenir 5 utilisateurs aléatoires différents de l'utilisateur actuel
        $users = $userRepository->findSuggestions($currentUser, 5);
        
        // Formater les données pour la réponse JSON
        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'avatarPath' =>  'images/avatars/default.png'
            ];
        }
        
        return $this->json($formattedUsers);
    }
    
    /**
     * Partager une liste de souhaits avec un utilisateur
     */
#[Route('/wishlist/add-collaborator', name: 'wishlist_add_collaborator', methods: ['POST'])]
public function addCollaborator(
    Request $request,
    EntityManagerInterface $entityManager,
    UserRepository $userRepository,
    WishlistRepository $wishlistRepository
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    // Vérifier que les paramètres nécessaires sont présents
    if (!isset($data['wishlistId']) || !isset($data['username'])) {
        return $this->json([
            'success' => false,
            'message' => 'Paramètres incomplets'
        ], 400);
    }

    $wishlistId = $data['wishlistId'];
    $username = $data['username'];

    // Récupérer la wishlist
    $wishlist = $wishlistRepository->find($wishlistId);
    if (!$wishlist) {
        return $this->json([
            'success' => false,
            'message' => 'Liste de souhaits introuvable'
        ], 404);
    }

    // Vérifier que l'utilisateur actuel est le propriétaire de la wishlist
    $currentUser = $this->getUser();
    if ($wishlist->getOwner() !== $currentUser) {
        return $this->json([
            'success' => false,
            'message' => 'Vous n\'êtes pas autorisé à modifier cette liste'
        ], 403);
    }

    // Récupérer l'utilisateur par son nom d'utilisateur
    $targetUser = $userRepository->findOneBy(['username' => $username]);
    if (!$targetUser) {
        return $this->json([
            'success' => false,
            'message' => 'Aucun utilisateur trouvé avec ce nom d\'utilisateur'
        ], 404);
    }

    // Vérifier si l'utilisateur est déjà collaborateur
    if ($wishlist->getCollaborators()->contains($targetUser)) {
        return $this->json([
            'success' => false,
            'message' => 'Cet utilisateur est déjà collaborateur'
        ], 400);
    }

    // Ajouter l'utilisateur comme collaborateur
    $wishlist->addCollaborator($targetUser);
    $entityManager->persist($wishlist);
    $entityManager->flush();

    return $this->json([
        'success' => true,
        'message' => 'Collaborateur ajouté avec succès',
        'username' => $targetUser->getUsername()
    ]);
}

     #[Route('/wishlist/share', name: 'wishlist_share', methods: ['POST'])]
     public function shareWishlist(
         Request $request, 
         EntityManagerInterface $entityManager, 
         UserRepository $userRepository, 
         WishlistRepository $wishlistRepository
     ): JsonResponse {
         // Récupérer les données JSON
         $data = json_decode($request->getContent(), true);
     
         // Débogage : afficher les données reçues
         file_put_contents('share_wishlist_debug.log', print_r($data, true));
     
         // Vérifier que tous les paramètres sont présents
         if (!isset($data['wishlistId']) || !isset($data['username'])) {
             return $this->json([
                 'success' => false,
                 'message' => 'Paramètres incomplets'
             ], 400);
         }
     
         $wishlistId = $data['wishlistId'];
         $username = $data['username'];
     
         // Vérifier que l'utilisateur est connecté
         $currentUser = $this->getUser();
         if (!$currentUser) {
             return $this->json([
                 'success' => false,
                 'message' => 'Vous devez être connecté'
             ], 401);
         }
     
         // Récupérer la wishlist
         $wishlist = $wishlistRepository->find($wishlistId);
         if (!$wishlist) {
             return $this->json([
                 'success' => false,
                 'message' => 'Liste de souhaits introuvable'
             ], 404);
         }
     
         // Vérifier que l'utilisateur est le propriétaire
         if ($wishlist->getOwner() !== $currentUser) {
             return $this->json([
                 'success' => false,
                 'message' => 'Vous n\'êtes pas autorisé à partager cette liste'
             ], 403);
         }
     
         // Chercher l'utilisateur exactement par son username
         $targetUser = $userRepository->findOneBy(['username' => $username]);
         
         // Vérifier si l'utilisateur existe
         if (!$targetUser) {
             return $this->json([
                 'success' => false,
                 'message' => 'Aucun utilisateur trouvé avec ce nom d\'utilisateur'
             ], 404);
         }
     
         // Vérifier si l'utilisateur n'est pas déjà collaborateur
         if ($wishlist->getCollaborators()->contains($targetUser)) {
             return $this->json([
                 'success' => false,
                 'message' => 'Cet utilisateur est déjà collaborateur'
             ], 400);
         }
     
         // Ajouter le collaborateur
         $wishlist->addCollaborator($targetUser);
         $targetUser->addCollaborativeWishlist($wishlist);
         
         // Enregistrer les modifications
         $entityManager->persist($wishlist);
         $entityManager->persist($targetUser);
         $entityManager->flush();
     
         return $this->json([
             'success' => true,
             'message' => 'Liste partagée avec succès',
             'username' => $targetUser->getUsername()
         ]);
     }    
}