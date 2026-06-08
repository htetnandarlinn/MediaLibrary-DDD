<?php

use App\Invoice\Infrastructure\InvoiceRepository;
use App\Notification\Infrastructure\Persistence\NotificationRepository;
use App\Reservation\Application\UseCase\CompleteReservationPaymentUseCase;
use App\Reservation\Infrastructure\Persistence\ReservationRepository;
use App\Shared\Database\Database;

require_once __DIR__ . '/vendor/autoload.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

$config = require __DIR__ . '/config/stripe.php';
\Stripe\Stripe::setApiKey($config['secret_key']);

$endpointSecret = $config['webhook_secret'];
$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigHeader,
        $endpointSecret
    );
} catch (\Throwable $e) {
    http_response_code(400);
    exit('Webhook Error: ' . $e->getMessage());
}

$db = Database::getConnection();
$reservationRepo = new ReservationRepository($db);
$invoiceRepo = new InvoiceRepository($db);
$notificationRepo = new NotificationRepository($db);
$completeReservationPaymentUseCase = new CompleteReservationPaymentUseCase(
    $reservationRepo,
    $invoiceRepo,
    $notificationRepo
);

/* =========================
   PAYMENT SUCCESS
========================= */
if (in_array($event->type, [
    'checkout.session.completed',
    'checkout.session.async_payment_succeeded'
], true)) {
    $session = $event->data->object;

    $reservationId = isset($session->metadata->reservation_id)
        ? (int) $session->metadata->reservation_id
        : null;

    $sessionId = $session->id ?? null;
    $paymentIntentId = $session->payment_intent ?? null;

    if ($reservationId && $sessionId) {
        $result = $completeReservationPaymentUseCase->execute($session);

        if (!$result['success']) {
            error_log('Stripe webhook payment handling failed: ' . $result['message']);
        }
    }
}

/* =========================
   PAYMENT FAILED
========================= */
if ($event->type === 'checkout.session.async_payment_failed') {
    $session = $event->data->object;

    $reservationId = isset($session->metadata->reservation_id)
        ? (int) $session->metadata->reservation_id
        : null;

    if ($reservationId) {
        $reservationRepo->update($reservationId, [
            'payment_status' => \App\Reservation\Domain\Entity\Reservation::PAYMENT_FAILED
        ]);

        try {
            $reservation = $reservationRepo->findById($reservationId);
            if ($reservation) {
                $notificationRepo->create(
                    $reservation->getUserId(),
                    'Payment failed',
                    'Your payment failed. Please try again.',
                    'warning'
                );
            }
        } catch (\Throwable $e) {
            error_log('Failed to create payment failed notification: ' . $e->getMessage());
        }
    }
}

http_response_code(200);
echo 'OK';
