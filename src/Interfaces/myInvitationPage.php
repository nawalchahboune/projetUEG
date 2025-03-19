<?php
namespace App\Interfaces;


namespace App\Interfaces;

use App\Entity\Invitation;


interface MyInvitationPage
{
    public function acceptInvitation(?Invitation $invitation): void;
}