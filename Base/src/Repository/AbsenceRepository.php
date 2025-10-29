<?php

namespace App\Repository;

use App\Entity\User\Absence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Absence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Absence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Absence[]    findAll()
 * @method Absence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbsenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Absence::class);
    }

    /**
     * @return array Returns an array of Company objects
     */
    public function findAllByCompany(int $value, string $orderBy = 'ASC'): array
    {
        return $this->createQueryBuilder('abs')
            ->join('abs.user', 'user')
            ->join('user.company', 'company')
            ->Where('company.id = :val')
            ->setParameter('val', $value)
            ->orderBy('abs.id', $orderBy)
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Company
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
