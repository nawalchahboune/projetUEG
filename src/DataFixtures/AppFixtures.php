<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Wishlist;
use App\Entity\Item;
use App\Entity\Proof;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création des utilisateurs
        $users = [];
        
        $admin = new User();
        $admin->setFirstName('Admin');
        $admin->setLastName('System');
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setLockStatus(false);
        $admin->setType('admin');
        $manager->persist($admin);
        $users[] = $admin;
        
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setFirstName('Prénom' . $i);
            $user->setLastName('Nom' . $i);
            $user->setUsername('user' . $i);
            $user->setEmail('user' . $i . '@example.com');
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password' . $i));
            $user->setLockStatus(false);
            $user->setType('user');
            $manager->persist($user);
            $users[] = $user;
        }
        
        // Création des wishlists
        $wishlists = [];
        
        foreach ($users as $key => $user) {
            // Chaque utilisateur aura 2 wishlists
            for ($i = 1; $i <= 2; $i++) {
                $wishlist = new Wishlist();
                $wishlist->setName('Liste de souhaits ' . $i . ' de ' . $user->getUsername());
                
                // Date limite aléatoire dans les 3 prochains mois
                $randomDays = rand(15, 90);
                $deadline = new \DateTime();
                $deadline->modify('+' . $randomDays . ' days');
                $wishlist->setDeadline($deadline);
                
                $wishlist->setOwner($user);
                
                // Ajouter des collaborateurs aléatoires
                $collaboratorsCount = rand(0, 2);
                $shuffledUsers = $users;
                shuffle($shuffledUsers);
                
                for ($j = 0; $j < $collaboratorsCount; $j++) {
                    if ($shuffledUsers[$j] !== $user) {
                        $wishlist->addCollaborator($shuffledUsers[$j]);
                    }
                }
                
                $manager->persist($wishlist);
                $wishlists[] = $wishlist;
            }
        }
        
        // Création des items
        foreach ($wishlists as $wishlist) {
            // Chaque wishlist aura entre 3 et 7 items
            $itemCount = rand(3, 7);
            
            for ($i = 1; $i <= $itemCount; $i++) {
                $item = new Item();
                $item->setName('Item ' . $i . ' - ' . $wishlist->getName());
                $item->setDescription('Description de l\'item ' . $i . ' appartenant à la liste ' . $wishlist->getName());
                $item->setPrice(rand(1000, 50000) / 100); // Prix entre 10 et 500 €
                $item->setHasPurchased(rand(0, 5) > 4); // 20% de chance d'être acheté
                
                // 80% de chance d'avoir une URL
                if (rand(1, 10) <= 8) {
                    $item->setUrl('https://example.com/product/' . uniqid());
                }
                
                $item->setWishlist($wishlist);
                $manager->persist($item);
                
                // Si l'item est marqué comme acheté, ajouter une preuve
                if ($item->hasPurchased()) {
                    // Choisir un acheteur aléatoire parmi les collaborateurs ou un autre utilisateur
                    $potentialBuyers = $users;
                    // Retirer le propriétaire de la liste des acheteurs potentiels
                    $potentialBuyers = array_filter($potentialBuyers, function($u) use ($wishlist) {
                        return $u !== $wishlist->getOwner();
                    });
                    
                    if (!empty($potentialBuyers)) {
                        $buyer = $potentialBuyers[array_rand($potentialBuyers)];
                        
                        $proof = new Proof();
                        $proof->setCongratsMessage('Félicitations! J\'ai acheté ce cadeau pour toi!');
                        $proof->setProof('preuve_' . uniqid() . '.jpg');
                        $proof->setBuyer($buyer);
                        $manager->persist($proof);
                    }
                }
            }
        }
        
        $manager->flush();
    }
}
