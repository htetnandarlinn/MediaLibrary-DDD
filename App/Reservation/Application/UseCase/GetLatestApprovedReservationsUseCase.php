<?php

namespace App\Reservation\Application\UseCase;

use App\Reservation\Domain\Repository\ReservationRepositoryInterface;

class GetLatestApprovedReservationsUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $repository
    ) {}

    public function execute(int $limit = 5): array
    {
        return $this->repository->findLatestApproved($limit);
    }
}
