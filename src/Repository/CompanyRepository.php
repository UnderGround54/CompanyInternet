<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Company>
 */
class CompanyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     *
     * @param User $user
     * @return QueryBuilder
     */
    public function findCompanyByUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->join('c.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());
    }
}
