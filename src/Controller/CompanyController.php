<?php

namespace App\Controller;

use App\Service\CompanyService\CompanyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/companies')]
final class CompanyController extends AbstractController
{
    public function __construct(private readonly CompanyService $companyService) {}

    #[Route('', methods: ['GET'])]
    public function getCompanies(Request $request): JsonResponse
    {
        return $this->companyService->getCompanies($request);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function findCompany(int $id): JsonResponse
    {
        return $this->companyService->findCompany($id);
    }
}
