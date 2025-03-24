<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends Person
{
    #[ORM\OneToOne(inversedBy: 'user')]
    private ?Company $Company = null;

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function getCompany(): ?Company
    {
        return $this->Company;
    }

    public function setCompany(?Company $Company): static
    {
        $this->Company = $Company;

        return $this;
    }
}
