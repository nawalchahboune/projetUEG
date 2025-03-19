<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
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

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ORM\Table(name: 'notification')]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $sender;
    
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'receivedNotifications')]
    private ?User $recipient;
    
    #[ORM\Column(type: 'string', enumType: NotificationType::class)]
    private NotificationType $type;
    
    #[ORM\ManyToOne(targetEntity: Invitation::class)]
    private ?Invitation $invitation = null;
    
    #[ORM\ManyToOne(targetEntity: Item::class)]
    private ?Item $item = null;
    
    #[ORM\Column(type: 'text')]
    private string $message = '';
    
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct(?User $sender, ?User $recipient, NotificationType $type)
    {
        $this->sender = $sender;
        $this->recipient = $recipient;
        $this->type = $type;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function send(): void
    {
        $this->type->handleNotification($this);
    }

    public function sendAnswerInvitationNotif(): void
    {
        if($this->invitation->isAccepted() == true)
        {
            $this->message = $this->sender->getUsername() ." a accepté votre invitation";
        }
        else
        {
            $this->message = $this->sender->getUsername() ." a refusé votre invitation";
        }
    }

    public function sendPurchaseNotif(): void
    {
        $this->message = $this->sender->getUsername() ." vous a acheté l'article " . $this->item->getName();
    }

    public function sendProofNotif(): void
    {
        $this->message ="Votre preuve a été validée pour l'item " . $this->item->getName();
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
    
    public function getType(): NotificationType
    {
        return $this->type;
    }
    
    public function setType(NotificationType $type): void
    {
        $this->type = $type;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
    
    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }
    
    public function setInvitation(?Invitation $invitation): void
    {
        $this->invitation = $invitation;
    }
    
    public function getItem(): ?Item
    {
        return $this->item;
    }
    
    public function setItem(?Item $item): void
    {
        $this->item = $item;
    }
    
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}