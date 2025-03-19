<?php

interface purchasePage
{
    public function addProof() : Bool;
    public function sendMessage(String $message) : void;
}