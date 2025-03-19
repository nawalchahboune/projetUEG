<?php

interface myWishlistListPage
{
   
    public function createWishlist(?Wishlist $wishlist): void;

    public function deleteWishlist(?Wishlist $wishlist): void;

    public function shareWishlist(?Wishlist $wishlist, Collection ?User $users): void;

    public function displayWishlist(?Wishlist $wishlist, Collection ?User $users): void; 
}