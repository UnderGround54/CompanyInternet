<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends Person
{
    public function getRoles(): array
    {
        return ['ROLE_CLIENT'];
    }
}
