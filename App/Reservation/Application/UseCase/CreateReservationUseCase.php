<?php

namespace App\Reservation\Application\UseCase;

use App\Catalog\Domain\Repository\CatalogRepositoryInterface;
use App\Reservation\Application\Service\ReservationPriceCalculator;
use App\Reservation\Domain\Entity\Reservation;
use App\Reservation\Domain\Repository\ReservationRepositoryInterface;
use App\Notification\Domain\Repository\NotificationRepositoryInterface;

class CreateReservationUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $repository,
        private CatalogRepositoryInterface $catalogRepository,
        private ReservationPriceCalculator $priceCalculator,
        private ?NotificationRepositoryInterface $notificationModel = null
    ) {}

    public function execute(int $userId, int $mediaId, int $days): array
    {
        if ($this->repository->existsPendingForUserAndMedia($userId, $mediaId)) {
            return [
                'success' => false,
                'message' => 'You already have a pending reservation for this item.'
            ];
        }

        $catalogItem = $this->catalogRepository->read($mediaId);

        if ($catalogItem === null) {
            return [
                'success' => false,
                'message' => 'Media item not found.'
            ];
        }

        $amountCents = $this->priceCalculator->calculateAmountCents($catalogItem->toArray(), $days);

        $reservation = Reservation::create($userId, $mediaId, $days, $amountCents);
        $reservationId = $this->repository->create($reservation);

        // Notification for admin is created by the presentation layer to include username/title

        return [
            'success' => true,
            'message' => 'Reservation request submitted successfully.',
            'reservation_id' => $reservationId,
            'payment_amount_cents' => $amountCents,
            'media_title' => $catalogItem->getTitle()
        ];
    }
}
