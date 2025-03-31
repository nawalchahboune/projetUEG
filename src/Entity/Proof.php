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
    private ?string $proofImagePath = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $buyer = null;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'proofs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Item $item = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct(string $message = null, string $proofImagePath = null, User $buyer = null, Item $item = null)
    {
        $this->congratsMessage = $message;
        $this->proofImagePath = $proofImagePath;
        $this->buyer = $buyer;
        $this->item = $item;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProofImagePath(): ?string
    {
        return $this->proofImagePath;
    }

    public function setProofImagePath(?string $proofImagePath): self
    {
        $this->proofImagePath = $proofImagePath;
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

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}