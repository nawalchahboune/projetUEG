<?php

namespace App\Interfaces;

use App\Entity\Item;

interface viewUserWishlist
{
    public function sortItemsAsc(): void;
    public function sortItemsDesc(): void;
}