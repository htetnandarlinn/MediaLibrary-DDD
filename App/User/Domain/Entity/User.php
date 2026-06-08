<?php

namespace App\User\Domain\Entity;

class User
{
    public function __construct(
        private int $id,
        private string $username,
        private string $email,
        private string $password,
        private string $role = 'user'
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
