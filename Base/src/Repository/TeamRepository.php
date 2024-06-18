<?php

namespace App\Repository;

use App\Entity\User\Team;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @return array Returns an array of Company objects
     */
    public function findByCompany(int $value, string $orderBy = 'ASC'): array
    {
        return $this->createQueryBuilder('team')
            ->Where('team.companyId = :val')
            ->setParameter('val', $value)
            ->orderBy('team.id', $orderBy)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array Returns an array of Company objects
     */
    public function findLeadersByCompany(int $value, string $orderBy = 'ASC'): array
    {
        return $this->createQueryBuilder('team')
            ->select('lead.id')
            ->join('team.lead', 'lead')
            ->Where('team.companyId = :val')
            ->setParameter('val', $value)
            ->orderBy('team.id', $orderBy)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findTeamByUser(User $user)
    {
        return $this->createQueryBuilder('team')
            ->Where('team.lead = :id')
            ->setParameter('id', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Team
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
