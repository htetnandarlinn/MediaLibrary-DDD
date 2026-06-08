<?php

namespace App\User\Application\UseCase;

use App\User\Application\DTO\UserDTO;
use App\User\Domain\Mapper\UserMapper;
use App\User\Domain\Repository\UserRepositoryInterface;

class GetUserByIdUseCase
{
    public function __construct(
        private UserRepositoryInterface $repo
    ) {}

    public function execute(string $identifier): ?UserDTO
    {
        $user = $this->repo->findByUsername($identifier)
            ?? $this->repo->findByEmail($identifier);

        return $user ? UserMapper::toDTO($user) : null;
    }
}