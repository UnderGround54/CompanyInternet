<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    public function paginate(string $entityClass, Request $request, array $criteria = [], array $orderBy = []): array
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(1, $request->query->getInt('limit', 10));

        $repository = $this->entityManager->getRepository($entityClass);
        $totalItems = $repository->count($criteria);
        $totalPages = ceil($totalItems / $limit);

        $items = $repository->findBy($criteria, $orderBy, $limit, ($page - 1) * $limit);

        return [
            'items' => $items,
            'page' => $page,
            'limit' => $limit,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}