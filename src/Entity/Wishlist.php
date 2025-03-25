<?php

namespace App\Entity;

use App\Repository\WishlistRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use MyWishlistPage;
use Symfony\Component\Validator\Constraints as Assert;
use ViewUserWishlist;
use App\Entity\Item;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: WishlistRepository::class)]
class Wishlist implements \App\Interfaces\ViewUserWishlist, \App\Interfaces\MyWishlistPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\OneToMany(mappedBy: 'wishlist', targetEntity: Item::class, orphanRemoval: true)]
    private Collection $items;

    #[ORM\ManyToOne(inversedBy: 'ownedWishlists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'collaborativeWishlists')]
    private Collection $collaborators;

    #[ORM\Column(length: 36, unique: true, nullable: true ,name: 'collaboration_token')]
    private ?string $collaborationToken = null;

    #[ORM\Column(length: 36, unique: true, nullable: true, name:'public_token')]
    private ?string $publicToken = null;

    // ...existing relationships and methods

    public function getCollaborationToken(): ?string
    {
        if ($this->collaborationToken === null) {
            $this->collaborationToken = Uuid::v4()->toRfc4122();
        }
        return $this->collaborationToken;
    }
    public function refreshCollaborationToken(): self
    {
        $this->collaborationToken = Uuid::v4()->toRfc4122();
        return $this;
    }

    public function getPublicToken(): ?string
    {
        if ($this->publicToken === null) {
            $this->publicToken = Uuid::v4()->toRfc4122();
        }
        return $this->publicToken;
    }
    public function setPublicToken(): void
    {
        if ($this->publicToken === null) {
            $this->publicToken = Uuid::v4()->toRfc4122();
        }
    }
    public function setCollaborationToken(){
        if ($this->collaborationToken === null) {
            $this->collaborationToken = Uuid::v4()->toRfc4122();
        }
    }

    public function refreshPublicToken(): self
    {
        $this->publicToken = Uuid::v4()->toRfc4122();
        return $this;
    }
// composer require symfony/uid
    public function __construct(?string $name=null, ?DateTimeInterface $deadline=null )
    {
        $this->name = $name;
        $this->deadline = $deadline;
        $this->items = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
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

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;
        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(?Item $item): void
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setWishlist($this);
        }
    }

    public function removeItem(?Item $item): void
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getWishlist() === $this) {
                $item->setWishlist(null);
            }
        }
    }
    public function editItem(?Item $item, string $newName, string $newDescription, int $newPrice, string $newUrl): void
    {
        $item->setName($newName);
        $item->setDescription($newDescription);
        $item->setPrice($newPrice);
        $item->setUrl($newUrl);
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    public function addCollaborator(User $collaborator): self
    {
        if (!$this->collaborators->contains($collaborator)) {
            $this->collaborators->add($collaborator);
        }

        return $this;
    }

    public function removeCollaborator(User $collaborator): self
    {
        $this->collaborators->removeElement($collaborator);
        return $this;
    }

    public function editWishlist(Wishlist $wishlist): void
    {
        $this->name = $wishlist->getName();
        $this->deadline = $wishlist->getDeadline();
    }

    public function delete(): void
    {
        // Cette méthode sera implémentée dans le service ou le contrôleur
    }

    public function sortItemsAsc(): void
    {
        $iterator = $this->items->getIterator();
        $itemsArray = $this->items->toArray();
        usort($itemsArray, function (Item $a, Item $b) {
            return ($a->getName() <=> $b->getName());
        });
        $this->items = new ArrayCollection($itemsArray);
    }

    public function sortItemsDesc(): void
    {
        $iterator = $this->items->getIterator();
        $itemsArray = $this->items->toArray();
        usort($itemsArray, function (Item $a, Item $b) {
            return ($b->getName() <=> $a->getName());
        });
        $this->items = new ArrayCollection($itemsArray);
        $this->items = new ArrayCollection(iterator_to_array($iterator));
    }
}
