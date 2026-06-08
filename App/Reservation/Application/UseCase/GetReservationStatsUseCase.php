<?php

namespace App\Reservation\Application\UseCase;

use App\Reservation\Domain\Entity\Reservation;
use App\Reservation\Domain\Repository\ReservationRepositoryInterface;

class GetReservationStatsUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return [
            'total' => $this->repository->countAll(),
            'approved' => $this->repository->countByStatus(Reservation::STATUS_APPROVED),
            'rejected' => $this->repository->countByStatus(Reservation::STATUS_REJECTED),
        ];
    }
}
