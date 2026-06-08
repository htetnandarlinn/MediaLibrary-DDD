<?php

namespace App\User\Application\UseCase;

use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\ValidationException;
use App\User\Application\DTO\ResponseDTO;
use App\User\Domain\Entity\User;
use App\User\Domain\Mapper\UserMapper;
use App\User\Domain\Repository\UserRepositoryInterface;

class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $repo
    ) {}

    public function execute(array $data): ResponseDTO
    {
        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        $errors = [];

        if ($this->repo->findByUsername($username)) {
            $errors['username'] = 'Username is already taken.';
        }

        if ($this->repo->findByEmail($email)) {
            $errors['email'] = 'Email is already registered.';
        }

        if ($errors) {
            throw new ValidationException('Validation failed', $errors);
        }

        $id = $this->repo->create([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user'
        ]);

        $user = $this->repo->read((int) $id);

        if (!$user instanceof User) {
            throw new NotFoundException('User creation failed.');
        }

        return new ResponseDTO(
            true,
            'Registration successful',
            UserMapper::toDTO($user)
        );
    }
}
