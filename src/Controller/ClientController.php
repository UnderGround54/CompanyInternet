<?php

namespace App\Controller;

use App\Service\ClientService\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/clients')]
final class ClientController extends AbstractController
{
    public function __construct(private readonly ClientService $clientService) {}

    #[Route('/company/{companyId}', methods: ['GET'])]
    public function getClients(Request $request): JsonResponse
    {
        return $this->clientService->getClients($request);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function findClient(int $id): JsonResponse
    {
        return $this->clientService->findClient($id);
    }

    #[Route('', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createClient(Request $request): JsonResponse
    {
        return $this->clientService->createOrUpdateClient(null, $request);
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function updateClient(int $id, Request $request): JsonResponse
    {
        return $this->clientService->createOrUpdateClient($id, $request);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteClient(int $id): JsonResponse
    {
        return $this->clientService->deleteClient($id);
    }
}
