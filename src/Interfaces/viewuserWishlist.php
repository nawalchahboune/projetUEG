<?php
namespace App\Interfaces;


interface ViewUserWishlist
{
    public function sortItemsAsc(): void;
    public function sortItemsDesc(): void;
}