<?php
namespace App\Dto;

class CompanyIdDto
{
    final public function __construct(
        public int $companyId,
    ) {}
}
