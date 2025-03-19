<?php

namespace App\Interfaces;

use App\Entity\Invitation;

interface myInvitationPage
{
    public function acceptInvitation(?Invitation $invitation): void;
}