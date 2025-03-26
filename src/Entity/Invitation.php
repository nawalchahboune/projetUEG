<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\InvitationRepository;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvitationRepository::class)]
#[ORM\Table(name: '`invitation`')]
class Invitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    private ?Wishlist $wishlist = null;

    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'myInvitations')]
    private ?User $receiver = null;

    private ?bool $accepted = null;

    public function __construct(?Wishlist $wishlist, ?User $sender, ?User $receiver)
    {
        if($receiver == null){
            throw new \Exception("Receiver can't be null");
        }
        $this->wishlist = $wishlist;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->accepted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWishlist(): ?Wishlist
    {
        return $this->wishlist;
    }

    public function setWishlist(?Wishlist $wishlist): static
    {
        $this->wishlist = $wishlist;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(?bool $accepted): static
    {
        $this->accepted = $accepted;
        if ($accepted) {
            $this->receiver->addCollaborativeWishlist($this->wishlist);
        } 

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        if($receiver == null){
            throw new \Exception("Receiver can't be null");
        }
        $this->receiver = $receiver;

        return $this;
    }

}
