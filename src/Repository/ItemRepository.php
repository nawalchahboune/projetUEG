<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function findTop3MostExpensiveItems(): array
    {
        return $this->createQueryBuilder('i')
            ->select('i.id', 'i.name', 'i.price', 'u.username as recipientName')
            ->join('i.wishlist', 'w')
            ->join('w.owner', 'u')  // the owner of the wishlist is the recipient
            ->where('i.hasPurchased = true')
            ->orderBy('i.price', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}
