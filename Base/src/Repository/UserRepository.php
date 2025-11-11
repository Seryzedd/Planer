<?php

namespace App\Repository;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use App\Repository\TeamRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

    private TokenStorageInterface $trackingStorage;

    public function __construct(ManagerRegistry $registry, TeamRepository $teamRepository, TokenStorageInterface $trackingStorage)
    {
        parent::__construct($registry, User::class);
        $this->teamRepository = $teamRepository;
        $this->trackingStorage = $trackingStorage;
    }

    /**
     * @return array Returns an array of Company objects
     */
    public function findByCompany(int $value, string $orderBy = 'ASC'): array
    {
        return $this->createQuery($value, $orderBy)
            ->getQuery()
            ->getResult()
        ;
    }

    public function createQuery(int $value, string $orderBy = 'ASC')
    {
        return $this->createQueryBuilder('user')
            ->join('user.company', 'company')
            ->Where('company.id = :val')
            ->setParameter('val', $value)
            ->orderBy('user.id', $orderBy)
        ;
    }

    public function getOtherUsers(int $value, string $orderBy = 'ASC')
    {
        return $this->createQueryBuilder('user')
            ->join('user.company', 'company')
            ->Where('company.id = :val')
            ->andWhere('user.id != :userid')
            ->setParameter('val', $value)
            ->setParameter('userid', $this->trackingStorage->getToken()->getUser()->getId())
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

    public function executeQuery(string $request)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery($request);
        $result = $query->getArrayResult();

        return $result;
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