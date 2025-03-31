<?php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
namespace App\Interfaces;


interface ViewUserWishlist
{
    public function sortItemsAsc(): void;
    public function sortItemsDesc(): void;
}