<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     *
     * @param User $user
     * @return QueryBuilder
     */
    public function findClientByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->join('c.companies', 'comp')
            ->join('comp.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());
    }
}
