<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\InvitationRepository;
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

    #[ORM\OneToMany(targetEntity: User::class, orphanRemoval: true, )]
    private Collection $receivers;

    private ?bool $accepted = null;

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