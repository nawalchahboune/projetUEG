<?php

namespace App\Entity;

use App\Repository\ProofRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProofRepository::class)]
class Proof
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $congratsMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $proof = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $buyer = null;

    public function __construct(string $message = null, string $proof = null, User $buyer = null)
    {
        $this->congratsMessage = $message;
        $this->proof = $proof;
        $this->buyer = $buyer;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProof(): ?string
    {
        return $this->proof;
    }

    public function setProof(?string $proof): self
    {
        $this->proof = $proof;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->congratsMessage;
    }

    public function setMessage(?string $message): self
    {
        $this->congratsMessage = $message;
        return $this;
    }
    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): self
    {
        $this->buyer = $buyer;
        return $this;
    }

    public function getCongratsMessage(): ?string
    {
        return $this->congratsMessage;
    }

    public function setCongratsMessage(string $congratsMessage): static
    {
        $this->congratsMessage = $congratsMessage;

        return $this;
    }
}