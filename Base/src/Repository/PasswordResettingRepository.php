<?php

namespace App\Repository;

use App\Entity\User\Security\PasswordResetting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * @extends ServiceEntityRepository<Company>
 *
 * @method Absence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Absence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Absence[]    findAll()
 * @method Absence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasswordResettingRepository extends ServiceEntityRepository implements ResetPasswordRequestRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetting::class);
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

    public function FindByToken(string $value): ?Company
    {
        // dump(new PasswordResetting()); die;
        return $this->createQueryBuilder('token')
            ->andWhere('token.hashedToken = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Create a new ResetPasswordRequest object.
     *
     * @param object $user        User entity - typically implements Symfony\Component\Security\Core\User\UserInterface
     * @param string $selector    A non-hashed random string used to fetch a request from persistence
     * @param string $hashedToken The hashed token used to verify a reset request
     */
    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {

    }

    /**
     * Get the unique user entity identifier from persistence.
     *
     * @param object $user User entity - typically implements Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUserIdentifier(object $user): string
    {
        return $user->getEmail();
    }

    /**
     * Save a reset password request entity to persistence.
     */
    public function persistResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {

    }

    /**
     * Get a reset password request entity from persistence, if one exists, using the request's selector.
     *
     * @param string $selector A non-hashed random string used to fetch a request from persistence
     */
    public function findResetPasswordRequest(string $selector): ?ResetPasswordRequestInterface
    {

    }

    /**
     * Get the most recent non-expired reset password request date for the user, if one exists.
     *
     * @param object $user User entity - typically implements Symfony\Component\Security\Core\User\UserInterface
     */
    public function getMostRecentNonExpiredRequestDate(object $user): ?\DateTimeInterface
    {
    }

    /**
     * Remove this reset password request from persistence and any other for this user.
     *
     * This method should remove this ResetPasswordRequestInterface and also all
     * other ResetPasswordRequestInterface objects in storage for the same user.
     */
    public function removeResetPasswordRequest(ResetPasswordRequestInterface $resetPasswordRequest): void
    {

    }

    /**
     * Remove all expired reset password request objects from persistence.
     *
     * @return int Number of request objects removed from persistence
     */
    public function removeExpiredResetPasswordRequests(): int
    {

    }
}