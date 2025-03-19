<?php

namespace App\Interfaces;

interface purchasePage
{
    public function addProof() : Bool;
    public function sendMessage(String $message) : void;
}