<?php
namespace App\Interfaces;


interface PurchasePage
{
    public function addProof() : Bool;
    public function sendMessage(String $message) : void;
}