<?php

namespace App\User\Application\DTO;

class UserDTO
{
    public function __construct(
        public ?int $id,
        public string $username,
        public string $email,
        public string $role = 'user'
    ) {}

    public function toArray(): array
    {
        return [
            'user_id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role
        ];
    }
}
