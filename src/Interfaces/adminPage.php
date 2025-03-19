<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

interface adminPage
{
    public function getTopExpensive() : ?Item;

    public functiongetTopWishlist() : Collection ?Wishlist;

    public function getUsers() : ?User;

    public function lockUser(?User $user) : void;

    public function unLock(?User $user) : void;

    public function deleteUser(?User $user) : void;

    public function getTop3ExpensiveItems() : Collection ?Item;

    public function getTop3WishlistsByValue() : Collection ?Wishlist;
}