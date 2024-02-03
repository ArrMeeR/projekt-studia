<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByIdentifier(string $identifier): ?User
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.username = :query')
            ->orWhere('u.email = :query')
            ->setParameter('query', $identifier)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function searchByUsername(string $username): array
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.username like :username')
            ->setParameter('username', '%' . $username . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }
}
