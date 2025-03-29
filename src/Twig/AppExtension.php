<?php

namespace App\Twig;

use App\Entity\Wishlist;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_wishlist_expired', [$this, 'isWishlistExpired']),
        ];
    }

    public function isWishlistExpired(Wishlist $wishlist): bool
    {
        if ($wishlist->getDeadline() === null) {
            return false;
        }
        
        return $wishlist->getDeadline() < new \DateTime();
    }
}

?>