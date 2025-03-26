<?php 

namespace App\Controller;

use App\Repository\ItemRepository;
use App\Repository\WishlistRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/top3', name: 'admin_top3')]
    public function top3(
        ItemRepository $itemRepo,
        WishlistRepository $wishlistRepo
    ): Response {
        // (a) Top-3 most expensive items bought for a wishlist
        $topExpensiveItems = $itemRepo->findTop3MostExpensiveItems();

        // (b) Top-3 wishlists by total value of purchased gifts
        $topWishlists = $wishlistRepo->findTop3WishlistsByTotalValue();

        return $this->render('admin/top3.html.twig', [
            'topItems' => $topExpensiveItems,
            'topWishlists' => $topWishlists,
        ]);
    }

    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/users', name: 'admin_users')]
    public function listUsers(UserRepository $userRepo): Response
    {
        $currentUser = $this->getUser();
        
        $allUsers = $userRepo->findAll();
        $users = array_filter($allUsers, function($user) use ($currentUser) {
        return $user->getId() !== $currentUser->getId();
         });

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{id}/lock', name: 'admin_user_lock', methods: ['POST'])]
    public function lockUser($id, UserRepository $userRepo, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepo->find($id);
        if ($user) {
            $user->setLockStatus(true);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User locked.');
        }
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/users/{id}/unlock', name: 'admin_user_unlock', methods: ['POST'])]
    public function unlockUser($id, UserRepository $userRepo, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepo->find($id);
        if ($user) {
            $user->setLockStatus(false);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User unlocked.');
        }
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/users/{id}/delete', name: 'admin_user_delete', methods: ['POST'])]
    public function deleteUser($id, UserRepository $userRepo, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepo->find($id);
        if ($user) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'User deleted.');
        }
        return $this->redirectToRoute('admin_users');
    }
}