<?php

namespace App\Reservation\Application\UseCase;

use App\Reservation\Domain\Repository\ReservationRepositoryInterface;

class GetAllReservationsUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
