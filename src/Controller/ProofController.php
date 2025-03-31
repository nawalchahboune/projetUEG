<?php
// src/Controller/ProofController.php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
namespace App\Controller;

use App\Entity\Item;
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
        // Récupération de l'item en base
        $item = $entityManager->getRepository(Item::class)->find($id);
        
        if (!$item) {
            throw new NotFoundHttpException('Item not found');
        }
        
        // Affichage du formulaire de preuve
        return $this->render('proof/proof.html.twig', [
            'itemId' => $id,
            'item' => $item,
        ]);
    }

    #[Route('/gift/proof', name: 'upload_proof', methods: ['POST'])]
    public function uploadProof(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $itemId = $request->request->get('itemId');
        $item = $entityManager->getRepository(Item::class)->find($itemId);
        
        if (!$item) {
            $this->addFlash('error', 'Invalid item');
            return $this->redirectToRoute('homepage'); // Redirection vers la page d'accueil ou autre
        }
        
        $uploadedFile = $request->files->get('proofFile');
        $congratsMsg = $request->request->get('congratsMsg');

        if ($uploadedFile) {
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

            try {
                $uploadedFile->move(
                    $this->getParameter('proofs_directory'),
                    $newFilename
                );
                
                // Met à jour le statut de l'item
                $item->setHasPurchased(true);
                
                // Création et configuration de l'entité Proof
                $proof = new Proof();
                $proof->setItem($item);
                $proof->setProofImagePath($newFilename);
                $proof->setMessage($congratsMsg);
                
                if ($this->getUser()) {
                    $proof->setBuyer($this->getUser());
                }
                
                $entityManager->persist($proof);
                $item->setProof($proof);

                $entityManager->flush();
                
                $this->addFlash('success', 'Proof uploaded successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Could not upload file. Please try again.');
                return $this->redirectToRoute('gift_proof_form', ['id' => $itemId]);
            }
        } else {
            $this->addFlash('error', 'No file selected. Please choose a file to upload.');
        }

        // Redirection vers le formulaire de preuve (ou une autre page)
        return $this->redirectToRoute('gift_proof_form', ['id' => $itemId]);
    }

    #[Route('/gift/proof/{id}/edit', name: 'edit_proof', methods: ['GET', 'POST'])]
    public function editProof(Request $request, Proof $proof, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        // Vérifie que l'utilisateur connecté est bien le buyer
        if (!$this->getUser() || $this->getUser()->getId() !== $proof->getBuyer()?->getId()) {
            throw $this->createAccessDeniedException('You are not allowed to edit this proof.');
        }

        if ($request->isMethod('POST')) {
            $congratsMsg = $request->request->get('congratsMsg');
            $uploadedFile = $request->files->get('proofFile');
            
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

                try {
                    $uploadedFile->move(
                        $this->getParameter('proofs_directory'),
                        $newFilename
                    );
                    $proof->setProofImagePath($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Could not upload file. Please try again.');
                }
            }
            $proof->setMessage($congratsMsg);

            $entityManager->flush();
            $this->addFlash('success', 'Proof updated successfully!');

            // Redirection vers le formulaire de preuve de l'item
            return $this->redirectToRoute('gift_proof_form', ['id' => $proof->getItem()->getId()]);
        }

        // Affichage du formulaire d'édition de la preuve
        return $this->render('proof/edit_proof.html.twig', [
            'proof' => $proof,
            'item' => $proof->getItem(),
        ]);
    }
}
