<?php

namespace App\Reservation\Application\UseCase;

use App\Reservation\Domain\Repository\ReservationRepositoryInterface;

class GetReservationsByStatusUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $repository
    ) {}

    public function execute(string $status): array
    {
        return $this->repository->findByStatus($status);
    }
}
