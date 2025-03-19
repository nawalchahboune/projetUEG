<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

enum NotificationType: string {
    case Invitation = 'invitation'; // accepted or refused
    case Purchase = 'purchase'; // accepted or refused
    case Proof = 'proof';

    public function handleNotification(Notification $notification): void
    {
        switch ($this) {
            case self::Invitation:
                $notification->sendAnswerInvitationNotif();
                break;

            case self::Purchase:
                $notification->sendPurchaseNotif();
                break;

            case self::Proof:
                $notification->sendProofNotif();
                break;
        }
    }
}

class Notification
{
    private ?User $sender;
    #[ORM\OneToOne(inversedBy: 'myNotifications', targetEntity: User::class, orphanRemoval: true)]
    private ?User $recipient;
    private NotificationType $type;
    private string $message;

    public function __construct(?User $sender, ?User $recipient, NotificationType $type)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->type = $type;
    }


    public function send(): void
    {
        $this->type->handleNotification($this);
    }

    public function sendAnswerInvitationNotif(?Invitation $invitation): void
    {
        if($invitation->$accepted == true)
        {
            $this->message = $this->$sender->$username ."a accépté votre invitation";
        }
        else
        {
            $this->message = $this->$sender->$username ."a refusé votre invitation";
        }
    }

    public function sendPurchaseNotif(?Item $item): void
    {
        $this->message = $this->$sender->$username ." vous a acheté l'article " . $item->$name;
    }

    public function sendProofNotif(?Item $item): void
    {
        $this->message ="Votre preuve a été validé pour l'item " . $item->$name;
    }
    
    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): void
    {
        $this->sender = $sender;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): void
    {
        $this->recipient = $recipient;
    }
}