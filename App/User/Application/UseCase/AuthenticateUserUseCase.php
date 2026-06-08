<?php

namespace App\User\Application\UseCase;

use App\Shared\Exception\NotFoundException;
use App\User\Application\DTO\UserDTO;
use App\User\Domain\Mapper\UserMapper;
use App\User\Domain\Repository\UserRepositoryInterface;

class AuthenticateUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $repo
    ) {}

    public function execute(string $usernameOrEmail, string $password): UserDTO
    {
        // Try to find by username first, then by email
        $user = $this->repo->findByUsername($usernameOrEmail);

        if (!$user) {
            $user = $this->repo->findByEmail($usernameOrEmail);
        }

        if (!$user) {
            throw new NotFoundException('invalid username/email');
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new NotFoundException('invalid password');
        }

        // Allow an admin override via environment variables when the DB lacks a role column
        $role = $user->getRole();

        $adminUsername = getenv('ADMIN_USERNAME');
        $adminEmail = getenv('ADMIN_EMAIL');

        if (
            ($adminUsername !== false && $adminUsername !== '' && $adminUsername === $user->getUsername()) ||
            ($adminEmail !== false && $adminEmail !== '' && $adminEmail === $user->getEmail())
        ) {
            $role = 'admin';
        }

        return new UserDTO(
            $user->getId(),
            $user->getUsername(),
            $user->getEmail(),
            $role
        );
    }
}
