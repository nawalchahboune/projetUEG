<?php
/**
 *
 * @authors 
 * - CHAHBOUNE Nawal (Binôme 15)
 * - GHALLAB Houda (Binôme 15)
 *
*/
namespace App\Controller;

use App\Entity\User;
use App\FormRegister\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;
class SecurityController extends AbstractController
{
    #[Route('/signup', name: 'app_signup',)]
    public function signup(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Check if user is already logged in
        if ($this->getUser()) {
            // Redirect to myWishlist page
            return $this->redirectToRoute('app_home');
        }
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle profile photo upload
            /*
            $profilePhotoFile = $form->get('profilePhoto')->getData();
            if ($profilePhotoFile) {
                $originalFilename = pathinfo($profilePhotoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePhotoFile->guessExtension();
                
                try {
                    $profilePhotoFile->move(
                        $this->getParameter('profile_photos_directory'),
                        $newFilename
                    );
                    $user->setPhoto($newFilename);
                } catch (\Exception $e) {
                    // Handle exception if file upload fails
                }
            }*/
            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form['plainPassword']['first']->getData())
            );
            $user->setEmail($form['email']->getData());
            $user->setLockStatus(false);
            $user->setType('user');
            $user->setUsername($form['username']->getData());
            $user->setFirstName($form['firstName']->getData());
            $user->setLastName($form['lastName']->getData());
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/signup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Check if user is already logged in
    if ($this->getUser()) {
        // Redirect to myWishlist page
        return $this->redirectToRoute('app_home');
    }
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
    // This method can be empty - it will be intercepted by the logout key on your firewall
    // The actual logout logic is handled by Symfony's security system
    throw new \LogicException(message: 'This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}