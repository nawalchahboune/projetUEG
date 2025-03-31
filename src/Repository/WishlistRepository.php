<?php
/**
 *
 * @authors 
 * - CHAHBOUNE Nawal (Binôme 15)
 * - GHALLAB Houda (Binôme 15)
 *
*/
namespace App\Repository;

use App\Entity\User;
use App\Entity\Wishlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wishlist>
 */
class WishlistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wishlist::class);
    }
    public function findTop3WishlistsByTotalValue(): array
{
    return $this->createQueryBuilder('w')
        ->select('w', 'SUM(i.price) as totalValue', 'u.username as ownerName')
        ->join('w.items', 'i') // Jointure avec les items
        ->join('w.owner', 'u') // Jointure avec le propriétaire (relation ManyToOne ou OneToMany)
        ->groupBy('w')
        ->orderBy('totalValue', 'DESC')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();
}

public function findCollaboratorWishlists(User $user): array
{
    return $this->createQueryBuilder('w')
        ->innerJoin('w.collaborators', 'c')
        ->where('c = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
}
}
