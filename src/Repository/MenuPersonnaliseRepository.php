<?php

namespace App\Repository;

use App\Entity\MenuPersonnalise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuPersonnalise>
 */
class MenuPersonnaliseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuPersonnalise::class);
    }

    /** @return MenuPersonnalise[] */
    public function findBySession(string $sessionId): array
    {
        return $this->findBy(['sessionId' => $sessionId], ['createdAt' => 'DESC']);
    }
}
