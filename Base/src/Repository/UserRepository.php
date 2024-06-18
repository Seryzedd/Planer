<?php

namespace App\Repository;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use App\Repository\TeamRepository;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Absence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Absence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Absence[]    findAll()
 * @method Absence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private teamRepository $teamRepository;

    public function __construct(ManagerRegistry $registry, TeamRepository $teamRepository)
    {
        parent::__construct($registry, User::class);
        $this->teamRepository = $teamRepository;
    }

    /**
     * @return array Returns an array of Company objects
     */
    public function findByCompany(int $value, string $orderBy = 'ASC'): array
    {
        return $this->createQueryBuilder('user')
            ->join('user.company', 'company')
            ->Where('company.id = :val')
            ->setParameter('val', $value)
            ->orderBy('user.id', $orderBy)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNotLeaderInCompany(int $value, string $orderBy = 'ASC')
    {
        
        $nots = $this->teamRepository->findLeadersByCompany($value);

        return $this->createQueryBuilder('user')
            ->join('user.company', 'company')
            ->Where('company.id = :val')
            ->andWhere('user.id NOT IN (:nots)')
            ->setParameter('val', $value)
            ->setParameter('nots', $nots)
            ->orderBy('user.id', $orderBy)
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