<?php

namespace App\Repository;

use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Visit>
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    public function countVisit(\DateInterval $interval): int
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('COUNT(DISTINCT v.ip)')
           ->where('v.visitedAt > :date')
           ->setParameter('date', (new \DateTime('now'))->sub($interval));
    
        return $qb->getQuery()->getSingleScalarResult();
    }
}
