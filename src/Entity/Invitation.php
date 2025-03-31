<?php
/**
 *
 * @authors 
 * - YAO Jean-David (Binôme 13)
 * - AROUISSI Khaoula (Binôme 13)
 */
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

    #[ORM\ManyToOne(targetEntity: Wishlist::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wishlist $wishlist = null;


    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'myInvitations')]
    private ?User $receiver = null;

    // Tant que l'utilisateur n'a pas accepté l'invitation, il n'est pas collaborateur et accepted est null
    // Quand l'utilisateur accepte l'invitation, il devient collaborateur et accepted est true
    // Quand l'utilisateur refuse l'invitation, il n'est pas collaborateur et accepted est false
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $accepted = null;


    public function __construct(?Wishlist $wishlist, ?User $sender, ?User $receiver)
    {
        if($receiver == null){
            throw new \Exception("Receiver can't be null");
        }
        $this->wishlist = $wishlist;
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->accepted = null;
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
