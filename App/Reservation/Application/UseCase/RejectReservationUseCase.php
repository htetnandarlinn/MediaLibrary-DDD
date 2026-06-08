<?php

namespace App\Reservation\Application\UseCase;

use App\Reservation\Domain\Repository\ReservationRepositoryInterface;
use App\Notification\Domain\Repository\NotificationRepositoryInterface;

class RejectReservationUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $repository,
        private ?NotificationRepositoryInterface $notificationModel = null
    ) {}

    public function execute(int $reservationId, int $adminId): array
    {
        $reservation = $this->repository->findById($reservationId);

        if ($reservation === null) {
            return [
                'success' => false,
                'message' => 'Reservation not found.'
            ];
        }

        try {
            $reservation->reject($adminId);
            $reservationId = $reservation->getId();

            if ($reservationId === null) {
                throw new \RuntimeException('Reservation ID is missing.');
            }

            $this->repository->update($reservationId, [
                'status' => $reservation->getStatus(),
                'approved_by' => $reservation->getApprovedBy(),
                'approved_at' => null,
                'rejected_at' => $reservation->getRejectedAt()?->format('Y-m-d H:i:s')
            ]);

            // Notify the user about rejection
            if ($this->notificationModel !== null) {
                $title = 'Reservation rejected';
                $message = sprintf('Your reservation for %s was rejected', $reservation->getMediaTitle() ?? 'the item');
                $this->notificationModel->create($reservation->getUserId(), $title, $message, 'warning');
            }

            return [
                'success' => true,
                'message' => 'Reservation rejected successfully.'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
