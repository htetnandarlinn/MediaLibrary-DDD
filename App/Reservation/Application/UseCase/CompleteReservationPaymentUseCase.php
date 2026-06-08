<?php

namespace App\Reservation\Application\UseCase;

use App\Notification\Domain\Repository\NotificationRepositoryInterface;
use App\Reservation\Domain\Repository\ReservationRepositoryInterface;
use App\Invoice\Domain\Repository\InvoiceRepositoryInterface;
use App\Shared\Helpers\InvoiceNumberGenerator;

class CompleteReservationPaymentUseCase
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private InvoiceRepositoryInterface $invoiceRepository,
        private NotificationRepositoryInterface $notificationRepository
    ) {}

    public function execute(object $session): array
    {
        $reservation = $this->reservationRepository->findByStripeSessionId($session->id);

        if ($reservation === null) {
            return [
                'success' => false,
                'message' => 'Reservation not found for this payment session.'
            ];
        }

        if (($session->payment_status ?? '') !== 'paid') {
            return [
                'success' => false,
                'message' => 'Payment is not completed yet.'
            ];
        }

        $reservationId = $reservation->getId();
        if ($reservationId === null) {
            return [
                'success' => false,
                'message' => 'Invalid reservation record.'
            ];
        }

        $this->reservationRepository->update($reservationId, [
            'payment_status' => \App\Reservation\Domain\Entity\Reservation::PAYMENT_COMPLETED,
            'payment_completed_at' => date('Y-m-d H:i:s'),
            'stripe_payment_intent_id' => $session->payment_intent
        ]);

        $existingInvoice = $this->invoiceRepository->findByReservationIdOrPaymentIntent(
            $reservationId,
            $session->payment_intent ?? null
        );

        if (!$existingInvoice) {
            $invoiceNumber = InvoiceNumberGenerator::generate();
            $invoiceId = $this->invoiceRepository->create([
                'invoice_number' => $invoiceNumber,
                'reservation_id' => $reservationId,
                'user_id' => $reservation->getUserId(),
                'payment_intent_id' => $session->payment_intent ?? null,
                'amount' => $reservation->getPaymentAmountCents() / 100,
                'currency' => $session->currency ?? 'usd',
                'status' => 'PAID'
            ]);
        } else {
            $invoiceId = (int) $existingInvoice['id'];
        }

        try {
            $this->notificationRepository->create(
                $reservation->getUserId(),
                'Payment received',
                'Your payment was received successfully.',
                'success'
            );
            $this->notificationRepository->create(
                null,
                'Payment received',
                sprintf('Payment received for reservation %d', $reservationId),
                'info'
            );
        } catch (\Throwable $e) {
            // ignore notification failures
        }

        return [
            'success' => true,
            'message' => 'Payment successful! Your invoice has been generated.',
            'invoice_id' => $invoiceId,
            'invoice_number' => $existingInvoice['invoice_number'] ?? $invoiceNumber
        ];
    }
}
