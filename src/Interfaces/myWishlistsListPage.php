<?php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
namespace App\Interfaces;


namespace App\Interfaces;

use App\Entity\Wishlist; // Adjust the namespace as needed
use App\Entity\User;
use Doctrine\Common\Collections\Collection;

interface MyWishlistsListPage
{
    public function createWishlist(string $title, \DateTimeInterface $deadline): ?Wishlist;

    public function deleteWishlist(?Wishlist $wishlist): void;

    public function shareWishlist(?Wishlist $wishlist, Collection $users): void;

    public function displayWishlist(?Wishlist $wishlist, Collection $users): void; 
}
