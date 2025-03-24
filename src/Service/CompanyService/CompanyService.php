<?php

namespace App\Service\CompanyService;

use App\Entity\Company;
use App\Entity\User;
use App\Service\PaginationService;
use App\Service\ResponseService;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
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
        private readonly Security $security
    ) {}

    public function getCompanies(Request $request): JsonResponse
    {
        $user = $this->security->getUser();

        if ($user instanceof User) {
            $queryBuilder = $this->entityManager->getRepository(Company::class)->findCompanyByUser($user);
            $pagination = $this->paginationService->paginate($queryBuilder, $request);
        } else {
            $pagination = $this->paginationService->paginate(Company::class, $request);
        }

        $pagination['items'] = $this->serializerService->serializeData($pagination['items'], 'company:read');

        return $this->responseService->success($pagination);
    }

    public function findCompany(int $id): JsonResponse
    {
        $user = $this->security->getUser();

        if ($id !== $user?->getCompany()?->getId()) {
            return $this->responseService->error([], 'Users not related to these Companies', Response::HTTP_NOT_FOUND);
        }

        $company = $this->entityManager->getRepository(Company::class)->find($id);;

        return $this->responseService->success(
            $this->serializerService->serializeData($company, 'company:read')
        );
    }
}