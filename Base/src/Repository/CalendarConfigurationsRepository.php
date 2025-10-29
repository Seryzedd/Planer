<?php

namespace App\Repository;

use App\Entity\CalendarConfigurations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalendarConfigurations>
 *
 * @method CalendarConfigurations|null find($id, $lockMode = null, $lockVersion = null)
 * @method CalendarConfigurations|null findOneBy(array $criteria, array $orderBy = null)
 * @method CalendarConfigurations[]    findAll()
 * @method CalendarConfigurations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalendarConfigurationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarConfigurations::class);
    }

//    /**
//     * @return CalendarConfigurations[] Returns an array of CalendarConfigurations objects
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

//    public function findOneBySomeField($value): ?CalendarConfigurations
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
