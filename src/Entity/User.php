<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User extends Person
{
    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }
}
