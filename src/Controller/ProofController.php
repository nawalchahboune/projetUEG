<?php
// src/Controller/ProofController.php

namespace App\Controller;

use App\Entity\Item; // Assuming you have an Item entity
use App\Entity\Proof;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProofController extends AbstractController
{
    #[Route('/gift/proof/{id}', name: 'gift_proof_form', methods: ['GET'])]
    public function showProofForm(int $id, EntityManagerInterface $entityManager): Response
    {
        // Fetch the actual item from database
        $item = $entityManager->getRepository(Item::class)->find($id);
        
        // Check if item exists
        if (!$item) {
            throw new NotFoundHttpException('Item not found');
        }
        
        // Render template with actual item data
        return $this->render('proof/proof.html.twig', [
            'itemId' => $id,
            'item' => $item, // Pass the actual item to the template
        ]);
    }

    #[Route('/gift/proof', name: 'upload_proof', methods: ['POST'])]
    public function uploadProof(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        // Get the item ID from the form
        $itemId = $request->request->get('itemId');
        
        // Validate the item exists
        $item = $entityManager->getRepository(Item::class)->find($itemId);
        
        if (!$item) {
            $this->addFlash('error', 'Invalid item');
            return $this->redirectToRoute('homepage'); // Or wherever appropriate
        }
        
        // Continue with file upload as before
        $uploadedFile = $request->files->get('proofFile');
        $congratsMsg = $request->request->get('congratsMsg');

        if ($uploadedFile) {
            // Create a safe filename
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

            try {
                $uploadedFile->move(
                    $this->getParameter('proofs_directory'),
                    $newFilename
                );
                
                // Update the item status
                $item->setStatus('purchased');
                
                // Create and populate the Proof entity
                $proof = new Proof();
                $proof->setItem($item);
                $proof->setProofImagePath($newFilename);
                $proof->setMessage($congratsMsg);
                
                // Set the current user as the buyer
                if ($this->getUser()) {
                    $proof->setBuyer($this->getUser());
                }
                
                $entityManager->persist($proof);
                $entityManager->flush();
                
                $this->addFlash('success', 'Proof uploaded successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Could not upload file. Please try again.');
                return $this->redirectToRoute('gift_proof_form', ['id' => $itemId]);
            }
        } else {
            $this->addFlash('error', 'No file selected. Please choose a file to upload.');
        }

        // Redirect back to the proof form or another page
        return $this->redirectToRoute('gift_proof_form', ['id' => $itemId]);
    }
}