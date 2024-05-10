<?php

namespace App\Repository;

use App\Entity\Client\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    /**
     * @return array Returns an array of Company objects
     */
    public function findByCompany(int $value, string $orderBy = 'ASC'): array
    {
        return $this->createQueryBuilder('project')
            ->join('project.client', 'client')
            ->Where('client.companyId = :val')
            ->setParameter('val', $value)
            ->orderBy('project.id', $orderBy)
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