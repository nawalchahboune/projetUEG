<?php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
namespace App\Interfaces;


namespace App\Interfaces;

use App\Entity\Invitation;


interface MyInvitationPage
{
    public function acceptInvitation(?Invitation $invitation): void;
}