<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseService
{
    public function success($results = null, string $message = null, int $statusCode = 200): JsonResponse
    {
        return $this->createResponse('success', $statusCode, $message, $results);
    }

    public function error(array $errors = [], string $message = null, int $statusCode = 400): JsonResponse
    {
        return $this->createResponse('error', $statusCode, $message, null, $errors);
    }

    private function createResponse(string $status, int $statusCode, string $message = null, $results = null, array $errors = []): JsonResponse
    {
        $responseData = [
            'status' => $status,
            'code'   => $statusCode,
        ];

        if ($message !== null) {
            $responseData['message'] = $message;
        }

        if ($results !== null) {
            $responseData['data'] = $results;
        }

        if (!empty($errors)) {
            $responseData['errors'] = $errors;
        }

        return new JsonResponse($responseData, $statusCode);
    }
}