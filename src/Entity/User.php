<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Interfaces\myInvitationPage;
use App\Interfaces\myWishlistListPage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use myInvitationPage as GlobalMyInvitationPage;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Item;
use App\Entity\Wishlist;
use myWishlistListPage as GlobalMyWishlistListPage;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, GlobalMyInvitationPage, GlobalMyWishlistListPage
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

    private ?Invitation $invitation = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Wishlist::class, orphanRemoval: true)]
    private Collection $ownedWishlists;

    #[ORM\ManyToMany(targetEntity: Wishlist::class, mappedBy: 'collaborators')]
    private Collection $collaborativeWishlists;

    #[ORM\OneToMany(mappedBy: 'receivers', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $myInvitations;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $myNotifications;



    public function __construct()
    {
        $this->ownedWishlists = new ArrayCollection();
        $this->collaborativeWishlists = new ArrayCollection();
        $this->myInvitations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function isLockStatus(): ?bool
    {
        return $this->lockStatus;
    }

    public function setLockStatus(bool $lockStatus): static
    {
        $this->lockStatus = $lockStatus;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

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

    // Required methods for UserInterface
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        if ($this->type === 'admin') {
            $roles[] = 'ROLE_ADMIN';
        }
        return array_unique($roles);
    }


    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    // MyInvitationPage interface methods
    public function acceptInvitation(?Invitation $invitation): void
    {
        $invitation->setAccepted(true);
    }
    // MyWishlistListPage interface methods
    public function createWishlist(string $title, ?\DateTimeInterface $deadline): ?Wishlist
    {
        $wishlist = new Wishlist($title, $deadline);
        $wishlist->setName($title);
        $wishlist->setDeadline($deadline);
        $wishlist->setOwner($this);
        $this->addOwnedWishlist($wishlist);
        return $wishlist;
    }

    public function deleteWishlist(?Wishlist $wishlist): void
    {
        $this->removeOwnedWishlist($wishlist);
        
    }

    public function shareWishlist(?Wishlist $wishlist, Collection $users): void
    {
        foreach ($users as $user) {
            $wishlist->addCollaborator($user);
        }
    }

    public function displayWishlist(?Wishlist $wishlist, Collection $users): void
    {
        // A IMPLEMENTER !
    }


}
