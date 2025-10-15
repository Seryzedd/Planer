<?php

namespace App\Repository;

use App\Entity\HoursSold;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HoursSold>
 *
 * @method HoursSold|null find($id, $lockMode = null, $lockVersion = null)
 * @method HoursSold|null findOneBy(array $criteria, array $orderBy = null)
 * @method HoursSold[]    findAll()
 * @method HoursSold[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HoursSoldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HoursSold::class);
    }

//    /**
//     * @return HoursSold[] Returns an array of HoursSold objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HoursSold
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
