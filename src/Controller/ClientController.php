<?php

namespace App\Controller;

use App\Service\ClientService\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/clients')]
final class ClientController extends AbstractController
{
    public function __construct(private readonly ClientService $clientService) {}

    #[Route('', methods: ['POST'])]
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
    public function createClient(Request $request): JsonResponse
    {
        return $this->clientService->createOrUpdateClient(null, $request);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function updateClient(int $id, Request $request): JsonResponse
    {
        return $this->clientService->createOrUpdateClient($id, $request);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteClient(int $id): JsonResponse
    {
        return $this->clientService->deleteClient($id);
    }
}
