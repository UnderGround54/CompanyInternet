<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Company;
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
     * @param $companyId
     * @return QueryBuilder
     */
    public function findClientCompany($companyId): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->join('c.companies', 'comp')
            ->where('comp.id = :companyId')
            ->setParameter('companyId', $companyId);
    }
}
