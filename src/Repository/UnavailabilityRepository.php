<?php

namespace App\Repository;

use App\Entity\Announcement;
use App\Entity\Unavailability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Unavailability>
 */
class UnavailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unavailability::class);
    }

    public function hasUnavailability(Announcement $announcement, \DateTimeInterface $startDate, \DateTimeInterface $endDate): bool
    {
        $qb = $this->createQueryBuilder('u');

        $count = (int) $qb->select('COUNT(u.id)')
            ->where('u.announcement = :announcement')
            ->andWhere('u.startDate <= :newEndDate')
            ->andWhere('u.endDate >= :newStartDate')
            ->setParameter('announcement', $announcement)
            ->setParameter('newStartDate', $startDate)
            ->setParameter('newEndDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult();
        return $count > 0;
    }

//    /**
//     * @return Unavailability[] Returns an array of Unavailability objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Unavailability
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
