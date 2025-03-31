<?php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
namespace App\Interfaces;


interface PurchasePage
{
    public function addProof() : Bool;
    public function sendMessage(String $message) : void;
}