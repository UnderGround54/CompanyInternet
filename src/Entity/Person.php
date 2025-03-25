<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\MappedSuperclass]
abstract class Person implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client:read', 'company:read'])]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client:read', 'client:write', 'company:read'])]
    protected ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client:read', 'client:write', 'company:read'])]
    protected ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client:write'])]
    protected ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['client:read', 'client:write', 'company:read'])]
    private ?string $job = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    abstract public function getRoles(): array;

    public function eraseCredentials(): void
    {
        // Méthode obligatoire, mais on ne fait rien ici
    }

    public function getUserIdentifier(): string
    {
        return $this->email; // Utilisé comme identifiant unique
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;

        return $this;
    }
}
