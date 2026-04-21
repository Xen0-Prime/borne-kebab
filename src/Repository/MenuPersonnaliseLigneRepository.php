<?php

namespace App\Repository;

use App\Entity\MenuPersonnaliseLigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuPersonnaliseLigne>
 */
class MenuPersonnaliseLigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuPersonnaliseLigne::class);
    }
}
