<?php

namespace App\Repository;

use App\Entity\CalendarEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User\User;

/**
 * @extends ServiceEntityRepository<CalendarEvent>
 */
class CalendarEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarEvent::class);
    }

    public function findMyCloseEvents(User $user)
    {
        $now = new \DateTime();
        $soon = (new \DateTime())->modify('+1 day');

        return $this->createQueryBuilder('c')
            ->innerJoin('c.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('c.StartAt BETWEEN :now AND :soon')
            ->andWhere('c.endAt < :now')
            ->setParameter('now', $now)
            ->setParameter('soon', $soon)
            ->setParameter('userId', $user->getId())
            ->orderBy('c.StartAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return CalendarEvent[] Returns an array of CalendarEvent objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CalendarEvent
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
