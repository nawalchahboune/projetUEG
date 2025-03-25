<?php
namespace App\Interfaces;

namespace App\Interfaces;

use App\Entity\Item;

interface MyWishlistPage
{
    public function addItem(?Item $item): void;
    public function editItem(?Item $item, string $newName, string $newDescription, int $newPrice, string $newUrl): void;
    public function removeItem(?Item $item): void;
}
