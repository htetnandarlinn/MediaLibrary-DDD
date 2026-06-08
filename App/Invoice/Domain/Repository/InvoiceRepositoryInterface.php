<?php

namespace App\Invoice\Domain\Repository;

interface InvoiceRepositoryInterface
{
    public function getByUserId(int $userId): array;

    public function findById(int $id): ?array;

    public function findAll(): array;

    public function findByReservationIdOrPaymentIntent(int $reservationId, ?string $paymentIntentId): ?array;

    public function create(array $data): int;
}
