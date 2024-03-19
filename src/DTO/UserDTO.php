<?php

namespace App\DTO;

use App\Core\DataTransferObjectManager\DTOInterface;

class UserDTO implements DTOInterface
{
    private ?int $id = null;
    private ?string $email = null;
    private ?string $password = null;
    private ?string $firstname = null;
    private ?string $lastname = null;
    private ?array $role = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    public function setRole(?array $role): void
    {
        $this->role = $role;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->role);
    }
}
