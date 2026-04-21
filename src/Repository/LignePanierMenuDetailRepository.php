<?php

namespace App\Repository;

use App\Entity\LignePanierMenuDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LignePanierMenuDetail>
 */
class LignePanierMenuDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LignePanierMenuDetail::class);
    }
}
