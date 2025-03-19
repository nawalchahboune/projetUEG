<?php

interface myWishlistPage
{
    public function addItem(?Item $item): void;
    public function editItem(?Item $item): void;
    public function removeItem(?Item $item): void;
}
