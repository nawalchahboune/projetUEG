<?php
/**
 *
 * @authors 
 * - CHAHBOUNE Nawal (Binôme 15)
 * - GHALLAB Houda (Binôme 15)
 *
*/
namespace App\Security\Voter;

use App\Entity\Item;
use App\Entity\User;
use App\Entity\Wishlist;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ItemVoter extends Voter
{
    public const DELETE = 'DELETE';
    public const EDIT = 'EDIT';
    public const ADD = 'ADD';
    public const SHARE = 'SHARE';
    
    protected function supports(string $attribute, $subject): bool
    {
        // Vérifie si l'attribut correspond à une action et si le sujet est une instance de Wishlist ou Item
        return in_array($attribute, [self::DELETE, self::EDIT, self::ADD , self::SHARE])
            && ($subject instanceof Wishlist || $subject instanceof Item);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false; // L'utilisateur n'est pas authentifié
        }
            return $this->canAccess($attribute, $subject, $user);
    }
private function canAccess(string $attribute, $subject, User $user): bool
{
    return !$user->isLockStatus();
}


    
}