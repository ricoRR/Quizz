<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<History>
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    public function countQuizz(\DateInterval $interval): int
    {
        $qb = $this->createQueryBuilder('h');
        $qb->select('COUNT(h)')
           ->where('h.created_at > :date')
           ->setParameter('date', (new \DateTime('now'))->sub($interval));
    
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    
}
