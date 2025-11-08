<?php

namespace App\Repository;

use App\Entity\AbstractDayPart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbstractDayPart>
 *
 * @method AbstractDayPart|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractDayPart|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractDayPart[]    findAll()
 * @method AbstractDayPart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbstractDayPartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractDayPart::class);
    }

//    /**
//     * @return AbstractDayPart[] Returns an array of AbstractDayPart objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AbstractDayPart
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
