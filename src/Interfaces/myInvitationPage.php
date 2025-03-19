<?php
namespace App\Interfaces;


use App\Entity\Invitation; // Adjust the namespace as needed


interface MyInvitationPage
{
    public function acceptInvitation(?Invitation $invitation): void;
}