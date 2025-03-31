<?php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
namespace App\Interfaces;

namespace App\Interfaces;

use App\Entity\Item;

interface MyWishlistPage
{
    public function addItem(?Item $item): void;
    public function editItem(?Item $item, string $newName, string $newDescription, int $newPrice, string $newUrl): void;
    public function removeItem(?Item $item): void;
}
