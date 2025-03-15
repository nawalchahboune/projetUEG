<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    private ?string $firstName = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    private ?string $lastName = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le nom d\'utilisateur est obligatoire')]
    private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(message: 'L\'email {{ value }} n\'est pas valide')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column]
    private ?bool $lockStatus = false;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $type = 'user';

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Wishlist::class, orphanRemoval: true)]
    private Collection $ownedWishlists;

    #[ORM\ManyToMany(targetEntity: Wishlist::class, mappedBy: 'collaborators')]
    private Collection $collaborativeWishlists;

    public function __construct()
    {
        $this->ownedWishlists = new ArrayCollection();
        $this->collaborativeWishlists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // ... les autres getters et setters que vous avez déjà ...

    /**
     * @return Collection<int, Wishlist>
     */
    public function getOwnedWishlists(): Collection
    {
        return $this->ownedWishlists;
    }

    public function addOwnedWishlist(Wishlist $wishlist): self
    {
        if (!$this->ownedWishlists->contains($wishlist)) {
            $this->ownedWishlists->add($wishlist);
            $wishlist->setOwner($this);
        }

        return $this;
    }

    public function removeOwnedWishlist(Wishlist $wishlist): self
    {
        if ($this->ownedWishlists->removeElement($wishlist)) {
            // set the owning side to null (unless already changed)
            if ($wishlist->getOwner() === $this) {
                $wishlist->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getCollaborativeWishlists(): Collection
    {
        return $this->collaborativeWishlists;
    }

    public function addCollaborativeWishlist(Wishlist $wishlist): self
    {
        if (!$this->collaborativeWishlists->contains($wishlist)) {
            $this->collaborativeWishlists->add($wishlist);
            $wishlist->addCollaborator($this);
        }

        return $this;
    }

    public function removeCollaborativeWishlist(Wishlist $wishlist): self
    {
        if ($this->collaborativeWishlists->removeElement($wishlist)) {
            $wishlist->removeCollaborator($this);
        }

        return $this;
    }
}