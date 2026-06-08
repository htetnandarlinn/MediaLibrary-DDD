<?php

namespace App\User\Infrastructure\Persistence;

use App\Shared\Exception\DatabaseException;
use App\Shared\Infrastructure\Persistence\BaseRepository;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\UserRepositoryInterface;
use PDO;
use PDOException;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'users', 'user_id');
    }

    protected function mapToModel(array $row): User
    {
        return new User(
            $row['user_id'],
            $row['username'],
            $row['email'],
            $row['password'],
            $row['role'] ?? 'user'
        );
    }

    public function findByUsername(string $username): ?User
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM users WHERE username = :username LIMIT 1'
            );

            $stmt->execute([
                'username' => $username
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row
                ? $this->mapToModel($row)
                : null;
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Unable to retrieve user by username.'
            );
        }
    }

    public function findByEmail(string $email): ?User
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT * FROM users WHERE email = :email LIMIT 1'
            );

            $stmt->execute([
                'email' => $email
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row
                ? $this->mapToModel($row)
                : null;
        } catch (PDOException $e) {
            throw new DatabaseException(
                'Unable to retrieve user by email.'
            );
        }
    }
}
