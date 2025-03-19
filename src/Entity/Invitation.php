<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\OneToMany(inversedBy: 'myInvitations', targetEntity: User::class, orphanRemoval: true)]
    private Collection $receivers;

    private ?boolval $accepted = null;

    public function __construct(?Wishlist $wishlist, ?User $sender, Collection $receivers)
    {
        $this->wishlist = $wishlist;
        $this->sender = $sender;
        $this->receivers = $receivers;
        $this->accepted = false;

        return $this;
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

        return $this;
    }

    public function getReceivers(): Collection
    {
        return $this->receivers;
    }

}