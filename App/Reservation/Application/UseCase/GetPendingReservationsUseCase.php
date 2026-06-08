<?php

namespace App\Reservation\Application\UseCase;

use App\Reservation\Domain\Repository\ReservationRepositoryInterface;

class GetPendingReservationsUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->findPending();
    }
}
