<?php

namespace App\Reservation\Application\UseCase;

use App\Reservation\Domain\Repository\ReservationRepositoryInterface;
use App\Notification\Domain\Repository\NotificationRepositoryInterface;

class ApproveReservationUseCase
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
            $reservation->approve($adminId);
            $reservationId = $reservation->getId();

            if ($reservationId === null) {
                throw new \RuntimeException('Reservation ID is missing.');
            }

            $this->repository->update($reservationId, [
                'status' => $reservation->getStatus(),
                'approved_by' => $reservation->getApprovedBy(),
                'approved_at' => $reservation->getApprovedAt()?->format('Y-m-d H:i:s'),
                'rejected_at' => null
            ]);

            // Notify the user about approval
            if ($this->notificationModel !== null) {
                $title = 'Reservation approved';
                $message = sprintf('Your reservation for %s was approved', $reservation->getMediaTitle() ?? 'the item');
                $this->notificationModel->create($reservation->getUserId(), $title, $message, 'success');
            }

            return [
                'success' => true,
                'message' => 'Reservation approved successfully.'
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
