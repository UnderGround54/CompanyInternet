<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function paginate(mixed $source, Request $request, array $criteria = [], array $orderBy = []): array
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 10));

        if ($source instanceof QueryBuilder) {
            return $this->getPaginationDataFromQuery($source, $page, $limit);
        }

        return $this->getPaginationDataFromRepository($source, $page, $limit, $criteria, $orderBy);
    }

    private function getPaginationDataFromRepository(string $entityClass, int $page, int $limit, array $criteria, array $orderBy): array
    {
        $repository = $this->entityManager->getRepository($entityClass);
        $totalItems = $repository->count($criteria);
        $totalPages = ceil($totalItems / $limit);

        $items = $repository->findBy($criteria, $orderBy, $limit, ($page - 1) * $limit);

        return $this->formatPaginationResponse($items, $page, $limit, $totalItems, $totalPages);
    }

    private function getPaginationDataFromQuery(QueryBuilder $queryBuilder, int $page, int $limit): array
    {

        $rootAliases = $queryBuilder->getRootAliases();
        $alias = $rootAliases[0] ?? 'e';

        $totalItems = (clone $queryBuilder)
            ->select("COUNT($alias.id)")
            ->getQuery()
            ->getSingleScalarResult();

        $totalPages = ceil($totalItems / $limit);

        $queryBuilder->setMaxResults($limit)->setFirstResult(($page - 1) * $limit);
        $items = $queryBuilder->getQuery()->getResult();

        return $this->formatPaginationResponse($items, $page, $limit, $totalItems, $totalPages);
    }

    private function formatPaginationResponse(array $items, int $page, int $limit, int $totalItems, int $totalPages): array
    {
        return [
            'items' => $items,
            'page' => $page,
            'limit' => $limit,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}