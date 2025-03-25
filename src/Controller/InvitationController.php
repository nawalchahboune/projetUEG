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
        return $this->render('invitation/index.html.twig', [
            'invitations' => $invitationRepository->findAll(),
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
        return $this->render('invitation/show.html.twig', [
            'invitation' => $invitation,
        ]);
    }

    /**
     * @Route("/invitations/{id}/edit", name="invitation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Invitation $invitation, EntityManagerInterface $entityManager): Response
    {
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
        if ($this->isCsrfTokenValid('delete'.$invitation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($invitation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('invitation_index');
    }
}