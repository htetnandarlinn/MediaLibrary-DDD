<?php

namespace App\User\Domain\Repository;

use App\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findByUsername(string $username): ?User;

    public function findByEmail(string $email): ?User;
}
 