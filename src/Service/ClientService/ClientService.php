<?php

namespace App\Service\ClientService;

use App\Dto\ClientDto;
use App\Dto\ClientListDto;
use App\Dto\CompanyIdDto;
use App\Entity\Company;
use App\Entity\Client;
use App\Service\EmailService;
use App\Service\PaginationService;
use App\Service\ResponseService;
use App\Service\SerializerService;
use App\Service\ValidateDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SerializerService $serializerService,
        private readonly ResponseService $responseService,
        private readonly ValidatorInterface $validator,
        private readonly PaginationService $paginationService,
        private readonly ValidateDataService  $validateDataService,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private EmailService  $emailService,
    ) {}

    public function getClients(Request $request): JsonResponse
    {
        $companyId = $request->attributes->get('companyId');

        $queryBuilder = $this->entityManager->getRepository(Client::class)->findClientCompany($companyId);

        $pagination = $this->paginationService->paginate($queryBuilder, $request);

        $pagination['items'] = $this->serializerService->serializeData($pagination['items'], 'client:read');

        return $this->responseService->success($pagination);
    }

    public function findClient(int $id): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->find($id);
        if (!$client) {
            return $this->responseService->error([], 'Client not found', Response::HTTP_NOT_FOUND);
        }

        return $this->responseService->success(
            $this->serializerService->serializeData($client, 'client:read')
        );
    }

    public function createOrUpdateClient(?int $id, Request $request): JsonResponse
    {
        $client = $id ? $this->entityManager->getRepository(Client::class)->find($id) : new Client();

        if ($id && !$client) {
            return $this->responseService->error([], 'Client not found', Response::HTTP_NOT_FOUND);
        }

        $clientDto = $this->serializerService->deserializeData($request->getContent(), ClientDto::class);

        $errors = $this->validator->validate($clientDto);
        if (count($errors) > 0) {
            return $this->responseService->error($this->validateDataService->formatValidationErrors($errors), 'Validation errors', Response::HTTP_BAD_REQUEST);
        }
        $company = $this->entityManager->getRepository(Company::class)->find(intval($clientDto->companyId));

        if (!$company) {
            return $this->responseService->error([], 'Company not found', Response::HTTP_NOT_FOUND);
        }

        $client->setName($clientDto->name);
        $client->setEmail($clientDto->email);
        $client->addCompany($company);
        if ($clientDto->password) {
            $this->emailService->sendUserCredentialsEmail($clientDto->email, $clientDto->name, $clientDto->password, "https://www.adrware.mg/");
            $hashedPassword = $this->passwordHasher->hashPassword($client, $clientDto->password);
            $client->setPassword($hashedPassword);
        }

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return $this->responseService->success(
            $this->serializerService->serializeData($client, 'client:read'),
            $id ? 'Client updated' : 'Client created',
            $id ? Response::HTTP_OK : Response::HTTP_CREATED
        );
    }

    public function deleteClient(int $id): JsonResponse
    {
        $client = $this->entityManager->getRepository(Client::class)->find($id);
        if (!$client) {
            return $this->responseService->error([], 'Client not found', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();

        return $this->responseService->success([], 'Client deleted', Response::HTTP_NO_CONTENT);
    }
}