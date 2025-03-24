<?php


namespace App\Service;

class ValidateDataService
{
    public function formatValidationErrors($errors): array
    {
        $errorList = [];
        foreach ($errors as $error) {
            $errorList[$error->getPropertyPath()] = $error->getMessage();
        }
        return $errorList;
    }
}
