<?php

namespace App\Repository;

use App\Entity\TchatRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User\User;

/**
 * @extends ServiceEntityRepository<TchatRoom>
 *
 * @method TchatRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method TchatRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method TchatRoom[]    findAll()
 * @method TchatRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TchatRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TchatRoom::class);
    }

    /**
     * @return TchatRoom[] Returns an array of TchatRoom objects
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->join('t.title', 'users')
            ->where('users.id IN (:val)')
            ->setParameter('val', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?TchatRoom
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
