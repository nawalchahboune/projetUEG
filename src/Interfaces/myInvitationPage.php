<?php

use App\Entity\Invitation; // Adjust the namespace as needed

interface myInvitationPage
{
    public function acceptInvitation(?Invitation $invitation): void;
}