<?php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
namespace App\Controller;

use App\Entity\Invitation;
use App\Form\InvitationType;
use App\Repository\InvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    #[Route("/invitations", name: "invitation_list", methods: ["GET"])]
    public function index(InvitationRepository $invitationRepository): Response
    {
        // Retrieve invitations where the current user is the receiver
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $invitations = $invitationRepository->findBy([
            'receiver' => $user
        ]);

        return $this->render('invitation/listshow.html.twig', [
            'invitations' => $invitations,
        ]);
    }

    #[Route("/invitations/new", name: "invitation_new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $invitation = new Invitation();
        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($invitation);
            $entityManager->flush();

            return $this->redirectToRoute('invitation_list');
        }

        return $this->render('invitation/new.html.twig', [
            'invitation' => $invitation,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/invitations/{id}", name: "invitation_show", methods: ["GET"])]
    public function show(Invitation $invitation): Response
    {
        // Optionally verify that the current user is the receiver of the invitation
        $user = $this->getUser();
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette invitation.');
        }

        return $this->render('invitation/show.html.twig', [
            'invitation' => $invitation,
        ]);
    }

    #[Route("/invitations/{id}/edit", name: "invitation_edit", methods: ["GET", "POST"])]
    public function edit(Request $request, Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        // Optionally verify that the current user is the receiver of the invitation
        $user = $this->getUser();
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette invitation.');
        }

        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('invitation_list');
        }

        return $this->render('invitation/edit.html.twig', [
            'invitation' => $invitation,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/invitations/{id}", name: "invitation_delete", methods: ["POST"])]
    public function delete(Request $request, Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        // CSRF verification and optionally ensure that the current user is the receiver
        $user = $this->getUser();
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette invitation.');
        }

        if ($this->isCsrfTokenValid('delete'.$invitation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invitation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('invitation_list');
    }

    #[Route("/invitations/{id}/accept", name: "invitation_accept", methods: ["POST"])]
    public function accept(Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Verify that the current user is the receiver of the invitation
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette invitation.");
        }

        // If the invitation has already been accepted, show an info message
        if ($invitation->isAccepted()) {
            $this->addFlash('info', 'Cette invitation a déjà été acceptée.');
            return $this->redirectToRoute('app_wishlists_index');
        }

        // Accept the invitation. The entity method will add the wishlist to the user's collaborative lists.
        $invitation->setAccepted(true);

        $entityManager->flush();

        $this->addFlash('success', 'Invitation acceptée. La wishlist a été ajoutée à vos listes partagées.');

        return $this->redirectToRoute('app_wishlists_index');
    }

    #[Route("/invitations/{id}/reject", name: "invitation_reject", methods: ["POST"])]
    public function reject(Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Verify that the current user is the receiver of the invitation
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette invitation.");
        }

        // If the invitation is already rejected or accepted, we might show a message
        if ($invitation->isAccepted() === false) {
            $this->addFlash('info', 'Cette invitation a déjà été rejetée.');
            return $this->redirectToRoute('invitation_list');
        }

        // Set invitation as rejected (you may choose to store false, or handle rejection in your own way)
        $invitation->setAccepted(false);
        $entityManager->flush();

        $this->addFlash('success', 'Invitation rejetée.');

        return $this->redirectToRoute('invitation_list');
    }
}
