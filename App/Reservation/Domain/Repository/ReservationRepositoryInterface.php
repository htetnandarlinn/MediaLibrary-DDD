<?php

namespace App\Reservation\Domain\Repository;

use App\Reservation\Domain\Entity\Reservation;

interface ReservationRepositoryInterface
{
    public function create(array|Reservation $reservation): int;

    public function findById(int $id): ?Reservation;

    public function findByUserId(int $userId): array;

    public function findPending(): array;

    public function findByStatus(string $status): array;

    public function findAll(): array;

    public function countAll(): int;

    public function countByStatus(string $status): int;

    public function findLatestApproved(int $limit = 5): array;

    public function existsPendingForUserAndMedia(int $userId, int $mediaId): bool;

    public function update(int $id, array $data): bool;

    public function findByStripeSessionId(string $sessionId): ?\App\Reservation\Domain\Entity\Reservation;

    public function isBookMedia(int $mediaId): bool;
}
