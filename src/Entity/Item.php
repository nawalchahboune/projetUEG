<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: 'Le prix doit être positif ou nul')]
    private ?float $price = null;

    #[ORM\Column]
    private ?bool $hasPurchased = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wishlist $wishlist = null;

    #[ORM\OneToOne(mappedBy: 'item', targetEntity: Proof::class, cascade: ['persist', 'remove'])]
    private ?Proof $proof = null;


    public function __construct(string $name = null, string $description = null, float $price = null, string $url = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->url = $url;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getHasPurchased(): ?bool
    {
        return $this->hasPurchased;
    }

    public function setHasPurchased(bool $hasPurchased): self
    {
        $this->hasPurchased = $hasPurchased;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getWishlist(): ?Wishlist
    {
        return $this->wishlist;
    }

    public function setWishlist(?Wishlist $wishlist): self
    {
        $this->wishlist = $wishlist;
        return $this;
    }

    public function hasPurchased(): ?bool
    {
        return $this->hasPurchased;
    }

    public function getProof(): ?Proof
    {
        return $this->proof;
    }

    public function setProof(?Proof $proof): self
    {
        // unset the owning side of the relation if necessary
        if ($proof === null && $this->proof !== null) {
            $this->proof->setItem(null);
        }

        // set the owning side of the relation if necessary
        if ($proof !== null && $proof->getItem() !== $this) {
            $proof->setItem($this);
        }

        $this->proof = $proof;

        return $this;
    }
}