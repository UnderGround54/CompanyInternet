<?php

namespace App\Service\CompanyService;

use App\Entity\Company;
use App\Service\PaginationService;
use App\Service\ResponseService;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerService $serializerService,
        private readonly ResponseService $responseService,
        private readonly PaginationService $paginationService,
    ) {}

    public function getCompanies(Request $request): JsonResponse
    {
        $pagination = $this->paginationService->paginate(Company::class, $request);
        $pagination['items'] = $this->serializerService->serializeData($pagination['items'], 'company:read');

        return $this->responseService->success($pagination);
    }

    public function findCompany(int $id): JsonResponse
    {
        $company = $this->entityManager->getRepository(Company::class)->find($id);
        if (!$company) {
            return $this->responseService->error([], 'Company not found', Response::HTTP_NOT_FOUND);
        }

        return $this->responseService->success(
            $this->serializerService->serializeData($company, 'company:read')
        );
    }
}