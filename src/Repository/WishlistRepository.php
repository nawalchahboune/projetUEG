<?php

namespace App\Repository;

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

    //    /**
    //     * @return Wishlist[] Returns an array of Wishlist objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Wishlist
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

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
}
