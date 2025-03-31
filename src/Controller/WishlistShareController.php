<?php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
namespace App\Controller;

use App\Entity\Wishlist;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/wishlist')]
class WishlistShareController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/add-collaborator', name: 'wishlist_add_collaborator', methods: ['POST'])]
    public function addCollaborator(Wishlist $wishlist, Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier que l'utilisateur est propriétaire ou admin
        if ($wishlist->getOwner() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->json([
                'success' => false,
                'message' => 'Vous n\'avez pas l\'autorisation de partager cette liste.'
            ], 403);
        }

        // Récupérer le username
        $username = $request->request->get('username');
        if (!$username) {
            return $this->json([
                'success' => false,
                'message' => 'Aucun username reçu.'
            ], 400);
        }

        // Vérifier si l'utilisateur existe
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'L\'utilisateur "' . $username . '" n\'existe pas.'
            ], 404);
        }

        // Vérifier si déjà collaborateur
        if ($wishlist->getCollaborators()->contains($user)) {
            return $this->json([
                'success' => false,
                'message' => 'Cet utilisateur est déjà collaborateur.'
            ], 400);
        }

        // Ajouter le collaborateur (relation ManyToMany)
        $wishlist->addCollaborator($user);
        $em->flush();

        return $this->json([
            'success' => true,
            'message' => 'Collaborateur ajouté avec succès.'
        ]);
    }
}
