<?php

namespace App\Reservation\Application\UseCase;

use App\Reservation\Domain\Repository\ReservationRepositoryInterface;

class GetUserReservationsUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $repository
    ) {}

    public function execute(int $userId): array
    {
        return $this->repository->findByUserId($userId);
    }
}
