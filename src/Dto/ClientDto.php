<?php
namespace App\Dto;

class ClientDto
{
    final public function __construct(
        public string $name,

        public string $email,

        public string $password,

        public int|string $companyId,

        public string $job,
    ) {}
}
