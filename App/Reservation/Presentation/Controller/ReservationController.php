<?php

namespace App\Reservation\Presentation\Controller;

use App\Catalog\Domain\Repository\CatalogRepositoryInterface;
use App\Notification\Application\UseCase\CreateNotificationUseCase;
use App\Notification\Application\UseCase\GetNotificationsForAdminUseCase;
use App\Reservation\Application\UseCase\ApproveReservationUseCase;
use App\Reservation\Application\UseCase\CompleteReservationPaymentUseCase;
use App\Reservation\Application\UseCase\CreateReservationUseCase;
use App\Reservation\Application\UseCase\GetAllReservationsUseCase;
use App\Reservation\Application\UseCase\GetLatestApprovedReservationsUseCase;
use App\Reservation\Application\UseCase\GetPendingReservationsUseCase;
use App\Reservation\Application\UseCase\GetReservationsByStatusUseCase;
use App\Reservation\Application\UseCase\GetReservationStatsUseCase;
use App\Reservation\Application\UseCase\GetUserReservationsUseCase;
use App\Reservation\Application\UseCase\RejectReservationUseCase;
use App\Reservation\Domain\Entity\Reservation;
use App\Reservation\Domain\Repository\ReservationRepositoryInterface;
use App\Reservation\Presentation\Request\ReservationRequest;
use App\Shared\Helpers\InvoiceNumberGenerator;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class ReservationController
{
    public function __construct(
        private CreateReservationUseCase $createReservationUseCase,
        private GetUserReservationsUseCase $getUserReservationsUseCase,
        private GetPendingReservationsUseCase $getPendingReservationsUseCase,
        private GetReservationStatsUseCase $getReservationStatsUseCase,
        private GetLatestApprovedReservationsUseCase $getLatestApprovedReservationsUseCase,
        private GetReservationsByStatusUseCase $getReservationsByStatusUseCase,
        private GetAllReservationsUseCase $getAllReservationsUseCase,
        private ApproveReservationUseCase $approveReservationUseCase,
        private RejectReservationUseCase $rejectReservationUseCase,
        private CreateNotificationUseCase $createNotificationUseCase,
        private GetNotificationsForAdminUseCase $getNotificationsForAdminUseCase,
        private CompleteReservationPaymentUseCase $completeReservationPaymentUseCase,
        private ReservationRequest $request,
        private ReservationRepositoryInterface $reservationRepository,
        private CatalogRepositoryInterface $catalogRepository
    ) {}

    public function index(): void
    {
        $pageTitle = 'Reservations';
        $section = 'reservation';

        if (empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=login');
            exit;
        }

        $user = $_SESSION['user'];
        $errors = [];
        $successMessage = null;
        $mediaId = filter_input(INPUT_GET, 'media_id', FILTER_VALIDATE_INT);
        $days = 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($mediaId === null || $mediaId === false) {
                $mediaId = filter_input(INPUT_POST, 'media_id', FILTER_VALIDATE_INT);
            }

            $days = filter_input(INPUT_POST, 'days', FILTER_VALIDATE_INT) ?: 1;

            $data = [
                'media_id' => $_POST['media_id'] ?? '',
                'days' => $_POST['days'] ?? ''
            ];

            $isValid = $this->request->validate($data);

            if (!$isValid) {
                $errors = $this->request->errors();
            } else {
                $reservationId = (int) $_POST['media_id'];
                $result = $this->createReservationUseCase->execute(
                    $user['user_id'],
                    $reservationId,
                    $days
                );

                if ($result['success']) {
                    $successMessage = 'Reservation request submitted successfully. An admin must approve your request before payment can be completed.';

                    try {
                        $username = $_SESSION['user']['username'] ?? 'A user';
                        $mediaTitle = $result['media_title'] ?? ('item ' . (int) $reservationId);
                        $title = 'New reservation request';
                        $message = sprintf('%s reserved %s', $username, $mediaTitle);
                        $adminLink = rtrim(BASE_URL, '/') . '/Public/index.php?page=reservation_admin_approvals';

                        $this->createNotificationUseCase->execute(
                            null,
                            $title,
                            $message,
                            'info',
                            $adminLink
                        );
                    } catch (\Throwable $e) {
                        // swallow notification errors
                    }
                } else {
                    $errors['general'] = $result['message'];
                }
            }
        }

        $reservations = $this->getUserReservationsUseCase->execute($user['user_id']);

        require BASE_PATH . '/App/Reservation/Presentation/View/index.php';
    }

    public function dashboard(): void
    {
        $pageTitle = 'Admin Dashboard';
        $section = 'reservation_admin';

        if (empty($_SESSION['user']) || strtolower($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/Public/index.php?page=catalog');
            exit;
        }

        $pendingReservations = $this->getPendingReservationsUseCase->execute();
        $stats = $this->getReservationStatsUseCase->execute();
        $latestApproved = $this->getLatestApprovedReservationsUseCase->execute(3);

        $adminNotifications = $this->getNotificationsForAdminUseCase->execute($_SESSION['user']['user_id']);
        $adminUnreadCount = count(array_filter($adminNotifications, fn($notification) => empty($notification['is_read'])));
        $adminNotifications = array_slice($adminNotifications, 0, 5);

        $pendingCount = count($pendingReservations);
        $totalReservations = $stats['total'] ?? 0;
        $approvedCount = $stats['approved'] ?? 0;
        $rejectedCount = $stats['rejected'] ?? 0;

        require BASE_PATH . '/App/Reservation/Presentation/View/admin_dashboard.php';
    }

    public function admin(): void
    {
        $pageTitle = 'Reservation Approvals';
        $section = 'reservation_admin_approvals';

        if (empty($_SESSION['user']) || strtolower($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/Public/index.php?page=catalog');
            exit;
        }

        $errors = [];
        $successMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
            $action = $_POST['action'] ?? '';

            if (!$reservationId) {
                $errors['general'] = 'Invalid reservation selection.';
            } else {
                $adminId = $_SESSION['user']['user_id'];

                if ($action === 'approve') {
                    $result = $this->approveReservationUseCase->execute($reservationId, $adminId);
                } else {
                    $result = $this->rejectReservationUseCase->execute($reservationId, $adminId);
                }

                if ($result['success']) {
                    $successMessage = $result['message'];
                } else {
                    $errors['general'] = $result['message'];
                }
            }
        }

        $reservations = $this->getPendingReservationsUseCase->execute();

        require BASE_PATH . '/App/Reservation/Presentation/View/admin.php';
    }

    public function requests(): void
    {
        $pageTitle = 'Reservation Requests';
        $section = 'reservation_admin';

        if (empty($_SESSION['user']) || strtolower($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/Public/index.php?page=catalog');
            exit;
        }

        $filter = strtolower(trim((string) (filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_STRING) ?? 'all')));
        $status = null;
        $statusLabel = 'All Requests';

        if ($filter === 'pending') {
            $status = \App\Reservation\Domain\Entity\Reservation::STATUS_PENDING;
            $statusLabel = 'Pending Requests';
        } elseif ($filter === 'approved') {
            $status = \App\Reservation\Domain\Entity\Reservation::STATUS_APPROVED;
            $statusLabel = 'Approved Requests';
        } elseif ($filter === 'rejected') {
            $status = \App\Reservation\Domain\Entity\Reservation::STATUS_REJECTED;
            $statusLabel = 'Rejected Requests';
        }

        $successMessage = null;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reservationId = filter_input(INPUT_POST, 'reservation_id', FILTER_VALIDATE_INT);
            $action = $_POST['action'] ?? '';

            if (!$reservationId) {
                $errors['general'] = 'Invalid reservation selection.';
            } else {
                $adminId = $_SESSION['user']['user_id'];

                if ($action === 'approve') {
                    $result = $this->approveReservationUseCase->execute($reservationId, $adminId);
                } elseif ($action === 'reject') {
                    $result = $this->rejectReservationUseCase->execute($reservationId, $adminId);
                } else {
                    $result = [
                        'success' => false,
                        'message' => 'Invalid action requested.'
                    ];
                }

                if ($result['success']) {
                    $successMessage = $result['message'];
                } else {
                    $errors['general'] = $result['message'];
                }
            }
        }

        if ($status === null) {
            $reservations = $this->getAllReservationsUseCase->execute();
        } else {
            $reservations = $this->getReservationsByStatusUseCase->execute($status);
        }

        require BASE_PATH . '/App/Reservation/Presentation/View/admin_requests.php';
    }

    public function checkout(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=login');
            exit;
        }

        $reservationId = filter_input(INPUT_GET, 'reservation_id', FILTER_VALIDATE_INT);
        if (!$reservationId) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=reservation');
            exit;
        }

        $reservation = $this->reservationRepository->findById($reservationId);
        if ($reservation === null || $reservation->getUserId() !== ($_SESSION['user']['user_id'] ?? null)) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=reservation');
            exit;
        }

        $item = $this->catalogRepository->read($reservation->getMediaId());
        if ($item === null) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=reservation');
            exit;
        }

        $config = require BASE_PATH . '/config/stripe.php';
        Stripe::setApiKey($config['secret_key']);

        $successUrl = BASE_URL . '/Public/index.php?page=payment_success&session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = BASE_URL . '/Public/index.php?page=payment_cancel&reservation_id=' . $reservationId;

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => sprintf(
                            'Reservation for %s (%d day%s)',
                            $item->getTitle() ?? 'Media item',
                            $reservation->getPaymentDays(),
                            $reservation->getPaymentDays() === 1 ? '' : 's'
                        ),
                    ],
                    'unit_amount' => $reservation->getPaymentAmountCents(),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'reservation_id' => (string) $reservationId,
                'media_id' => (string) $reservation->getMediaId(),
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);

        $this->reservationRepository->update($reservationId, [
            'stripe_session_id' => $session->id,
            'payment_status' => Reservation::PAYMENT_PENDING,
        ]);

        header('Location: ' . $session->url);
        exit;
    }

    public function cancel(): void
    {
        $pageTitle = 'Payment Cancelled';
        require BASE_PATH . '/App/Reservation/Presentation/View/payment_cancel.php';
    }

    public function success(): void
    {
        $sessionId = filter_input(INPUT_GET, 'session_id', FILTER_SANITIZE_STRING);

        if (!$sessionId) {
            header('Location: ' . BASE_URL . '/Public/index.php?page=reservation');
            exit;
        }

        $config = require BASE_PATH . '/config/stripe.php';
        Stripe::setApiKey($config['secret_key']);

        $session = Session::retrieve($sessionId);
        $result = $this->completeReservationPaymentUseCase->execute($session);

        $pageTitle = $result['success'] ? 'Payment Completed' : 'Payment Status';
        $message = $result['message'];
        $successMessage = $result['success'];

        require BASE_PATH . '/App/Reservation/Presentation/View/payment_success.php';
    }
}
