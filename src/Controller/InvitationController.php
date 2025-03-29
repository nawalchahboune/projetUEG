<?php

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
    /**
     * @Route("/invitations", name="invitation_index", methods={"GET"})
     */
    public function index(InvitationRepository $invitationRepository): Response
    {
        // Récupérer les invitations dont l'utilisateur connecté est le récepteur
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        $invitations = $invitationRepository->findBy([
            'receiver' => $user
        ]);

        return $this->render('invitation/index.html.twig', [
            'invitations' => $invitations,
        ]);
    }

    /**
     * @Route("/invitations/new", name="invitation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $invitation = new Invitation();
        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($invitation);
            $entityManager->flush();

            return $this->redirectToRoute('invitation_index');
        }

        return $this->render('invitation/new.html.twig', [
            'invitation' => $invitation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/invitations/{id}", name="invitation_show", methods={"GET"})
     */
    public function show(Invitation $invitation): Response
    {
        // Optionnel : vérifier que l'utilisateur connecté est bien le destinataire de l'invitation
        $user = $this->getUser();
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette invitation.');
        }

        return $this->render('invitation/show.html.twig', [
            'invitation' => $invitation,
        ]);
    }

    /**
     * @Route("/invitations/{id}/edit", name="invitation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        // Optionnel : vérifier que l'utilisateur connecté est bien le destinataire de l'invitation
        $user = $this->getUser();
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette invitation.');
        }

        $form = $this->createForm(InvitationType::class, $invitation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('invitation_index');
        }

        return $this->render('invitation/edit.html.twig', [
            'invitation' => $invitation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/invitations/{id}", name="invitation_delete", methods={"POST"})
     */
    public function delete(Request $request, Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        // Vérification CSRF et éventuellement vérification que l'utilisateur connecté est bien le destinataire
        $user = $this->getUser();
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette invitation.');
        }

        if ($this->isCsrfTokenValid('delete'.$invitation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invitation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('invitation_index');
    }

    #[Route('/invitations/{id}/accept', name: 'invitation_accept', methods: ['POST'])]
    public function accept(Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // Vérifier que l'utilisateur connecté est bien le destinataire de l'invitation
        if ($invitation->getReceiver() !== $user) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette invitation.");
        }
        
        // Si l'invitation a déjà été acceptée, on affiche un message d'information
        if ($invitation->isAccepted()) {
            $this->addFlash('info', 'Cette invitation a déjà été acceptée.');
            return $this->redirectToRoute('app_wishlists_index');
        }
        
        // Accepter l'invitation : cette méthode dans l'entité ajoutera la wishlist aux listes collaboratives de l'utilisateur
        $invitation->setAccepted(true);
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Invitation acceptée. La wishlist a été ajoutée à vos listes partagées.');
        
        return $this->redirectToRoute('app_wishlists_index');
    }

    

}
