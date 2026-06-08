<?php

namespace App\User\Domain\Mapper;

use App\User\Application\DTO\UserDTO;
use App\User\Domain\Entity\User;

class UserMapper
{
    public static function toDTO(User $user): UserDTO
    {
        return new UserDTO(
            $user->getId(),
            $user->getUsername(),
            $user->getEmail(),
            $user->getRole()
        );
    }
}
