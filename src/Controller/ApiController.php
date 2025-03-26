<?php
// src/Controller/ApiController.php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wishlist;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $users = $userRepository->searchByUsername($query);
        
        // Formater les données pour la réponse JSON
        $formattedUsers = [];
        foreach ($users as $user) {
            $formattedUsers[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'avatarPath' => $user->getAvatarPath() ?? 'images/avatars/default.jpg'
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
                'avatarPath' => $user->getAvatarPath() ?? 'images/avatars/default.jpg'
            ];
        }
        
        return $this->json($formattedUsers);
    }
    
    /**
     * Partager une liste de souhaits avec un utilisateur
     */
    #[Route('/wishlist/share', name: 'wishlist_share', methods: ['POST'])]
    public function shareWishlist(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, WishlistRepository $wishlistRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['wishlistId']) || !isset($data['username'])) {
            return $this->json([
                'success' => false,
                'message' => 'Données manquantes'
            ], 400);
        }
        
        // Vérifier que la liste existe
        $wishlist = $wishlistRepository->find($data['wishlistId']);
        if (!$wishlist) {
            return $this->json([
                'success' => false,
                'message' => 'Liste de souhaits introuvable'
            ], 404);
        }
        
        // Vérifier que l'utilisateur actuel est le propriétaire de la liste
        if ($wishlist->getUser() !== $this->getUser()) {
            return $this->json([
                'success' => false,
                'message' => 'Vous n\'êtes pas autorisé à partager cette liste'
            ], 403);
        }
        
        // Trouver l'utilisateur avec qui partager
        $targetUser = $userRepository->findOneBy(['username' => $data['username']]);
        if (!$targetUser) {
            return $this->json([
                'success' => false,
                'message' => 'Utilisateur introuvable'
            ], 404);
        }
        
        // Vérifier que l'utilisateur ne partage pas avec lui-même
        if ($targetUser === $this->getUser()) {
            return $this->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas partager avec vous-même'
            ], 400);
        }
        
        // Ajouter l'utilisateur à la liste des personnes avec qui la liste est partagée
        // Note: Cette partie dépend de votre implémentation exacte du partage de liste
        // Supposons que vous avez une méthode addSharedUser dans l'entité Wishlist
        try {
            // Exemple - adaptez selon votre modèle de données
            $wishlist->addSharedUser($targetUser);
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'message' => 'Liste partagée avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Erreur lors du partage: ' . $e->getMessage()
            ], 500);
        }
    }
}