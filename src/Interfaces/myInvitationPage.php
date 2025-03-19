<?php

interface myInvitationPage
{
    public function acceptInvitation(?Invitation $invitation): void;
}