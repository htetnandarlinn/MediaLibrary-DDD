<?php

use App\Catalog\Presentation\Controller\CatalogController;
use App\Reservation\Presentation\Controller\ReservationController;
use App\Suggestion\Presentation\Controller\SuggestController;
use App\User\Presentation\Controller\UserController;

$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'suggest':
        $controller = new SuggestController(
            $sendSuggestionUseCase,
            $validator,
            $suggestionRequest,
            $getCategories,
            $getSuggestionFormats,
            $getSuggestionGenres
        );
        $controller->index();
        break;

    case 'details':
        $controller = new CatalogController($catalogPageService, $getRandomCatalog, $catalogItemService);
        $controller->details();
        break;

    case 'catalog':
        $controller = new CatalogController($catalogPageService, $getRandomCatalog, $catalogItemService);
        $controller->index();
        break;

    case 'login':
        $controller = new UserController($authenticateUserUseCase, $registerUserUseCase);
        $controller->login($loginUserRequest, $validator);
        break;

    case 'register':
        $controller = new UserController($authenticateUserUseCase, $registerUserUseCase);
        $controller->register($registerUserRequest, $validator);
        break;

    case 'reservation':
        $controller = new ReservationController(
            $createReservationUseCase,
            $getUserReservationsUseCase,
            $getPendingReservationsUseCase,
            $getReservationStatsUseCase,
            $getLatestApprovedReservationsUseCase,
            $getReservationsByStatusUseCase,
            $getAllReservationsUseCase,
            $approveReservationUseCase,
            $rejectReservationUseCase,
            $createNotificationUseCase,
            $getNotificationsForAdminUseCase,
            $completeReservationPaymentUseCase,
            $reservationRequest,
            $reservationRepo,
            $catalogRepo
        );
        $controller->index();
        break;

    case 'invoices':
        $controller = new \App\Invoice\Presentation\Controller\InvoiceController(
            $getInvoicesForUserUseCase,
            $getInvoiceByIdUseCase,
            $createNotificationUseCase
        );
        $controller->index();
        break;

    case 'invoice_show':
        $controller = new \App\Invoice\Presentation\Controller\InvoiceController(
            $getInvoicesForUserUseCase,
            $getInvoiceByIdUseCase,
            $createNotificationUseCase
        );
        $controller->show();
        break;

    case 'invoice_download':
        $controller = new \App\Invoice\Presentation\Controller\InvoiceController(
            $getInvoicesForUserUseCase,
            $getInvoiceByIdUseCase,
            $createNotificationUseCase
        );
        $controller->download();
        break;

    case 'reservation_admin':
        $controller = new ReservationController(
            $createReservationUseCase,
            $getUserReservationsUseCase,
            $getPendingReservationsUseCase,
            $getReservationStatsUseCase,
            $getLatestApprovedReservationsUseCase,
            $getReservationsByStatusUseCase,
            $getAllReservationsUseCase,
            $approveReservationUseCase,
            $rejectReservationUseCase,
            $createNotificationUseCase,
            $getNotificationsForAdminUseCase,
            $completeReservationPaymentUseCase,
            $reservationRequest,
            $reservationRepo,
            $catalogRepo
        );
        $controller->dashboard();
        break;

    case 'reservation_admin_approvals':
        $controller = new ReservationController(
            $createReservationUseCase,
            $getUserReservationsUseCase,
            $getPendingReservationsUseCase,
            $getReservationStatsUseCase,
            $getLatestApprovedReservationsUseCase,
            $getReservationsByStatusUseCase,
            $getAllReservationsUseCase,
            $approveReservationUseCase,
            $rejectReservationUseCase,
            $createNotificationUseCase,
            $getNotificationsForAdminUseCase,
            $completeReservationPaymentUseCase,
            $reservationRequest,
            $reservationRepo,
            $catalogRepo
        );
        $controller->admin();
        break;

    case 'reservation_admin_requests':
        $controller = new ReservationController(
            $createReservationUseCase,
            $getUserReservationsUseCase,
            $getPendingReservationsUseCase,
            $getReservationStatsUseCase,
            $getLatestApprovedReservationsUseCase,
            $getReservationsByStatusUseCase,
            $getAllReservationsUseCase,
            $approveReservationUseCase,
            $rejectReservationUseCase,
            $createNotificationUseCase,
            $getNotificationsForAdminUseCase,
            $completeReservationPaymentUseCase,
            $reservationRequest,
            $reservationRepo,
            $catalogRepo
        );
        $controller->requests();
        break;

    case 'admin_invoices':
        $controller = new \App\Admin\Presentation\Controller\AdminInvoiceController(
            $getAllInvoicesUseCase,
            $getInvoiceByIdUseCase,
            $createNotificationUseCase
        );
        $controller->index();
        break;

    case 'checkout':
        $controller = new ReservationController(
            $createReservationUseCase,
            $getUserReservationsUseCase,
            $getPendingReservationsUseCase,
            $getReservationStatsUseCase,
            $getLatestApprovedReservationsUseCase,
            $getReservationsByStatusUseCase,
            $getAllReservationsUseCase,
            $approveReservationUseCase,
            $rejectReservationUseCase,
            $createNotificationUseCase,
            $getNotificationsForAdminUseCase,
            $completeReservationPaymentUseCase,
            $reservationRequest,
            $reservationRepo,
            $catalogRepo
        );
        $controller->checkout();
        break;

    case 'payment_cancel':
        $controller = new ReservationController(
            $createReservationUseCase,
            $getUserReservationsUseCase,
            $getPendingReservationsUseCase,
            $getReservationStatsUseCase,
            $getLatestApprovedReservationsUseCase,
            $getReservationsByStatusUseCase,
            $getAllReservationsUseCase,
            $approveReservationUseCase,
            $rejectReservationUseCase,
            $createNotificationUseCase,
            $getNotificationsForAdminUseCase,
            $completeReservationPaymentUseCase,
            $reservationRequest,
            $reservationRepo,
            $catalogRepo
        );
        $controller->cancel();
        break;

    case 'payment_success':
        $controller = new ReservationController(
            $createReservationUseCase,
            $getUserReservationsUseCase,
            $getPendingReservationsUseCase,
            $getReservationStatsUseCase,
            $getLatestApprovedReservationsUseCase,
            $getReservationsByStatusUseCase,
            $getAllReservationsUseCase,
            $approveReservationUseCase,
            $rejectReservationUseCase,
            $createNotificationUseCase,
            $getNotificationsForAdminUseCase,
            $completeReservationPaymentUseCase,
            $reservationRequest,
            $reservationRepo,
            $catalogRepo
        );
        $controller->success();
        break;

    case 'admin_invoice_view':
        $controller = new \App\Admin\Presentation\Controller\AdminInvoiceController(
            $getAllInvoicesUseCase,
            $getInvoiceByIdUseCase,
            $createNotificationUseCase
        );
        $controller->view();
        break;

    case 'admin_invoice_download':
        $controller = new \App\Admin\Presentation\Controller\AdminInvoiceController(
            $getAllInvoicesUseCase,
            $getInvoiceByIdUseCase,
            $createNotificationUseCase
        );
        $controller->download();
        break;

    case 'logout':
        $controller = new UserController($authenticateUserUseCase, $registerUserUseCase);
        $controller->logout();
        break;

    /* ===========================
       NOTIFICATIONS (USER VIEW)
    =========================== */

    case 'notifications':
        $controller = new \App\Notification\Presentation\Controller\NotificationController(
            $getNotificationsForUserUseCase,
            $getNotificationsForAdminUseCase,
            $markNotificationReadUseCase
        );
        $controller->index();
        break;

    case 'notifications_mark':
        $controller = new \App\Notification\Presentation\Controller\NotificationController(
            $getNotificationsForUserUseCase,
            $getNotificationsForAdminUseCase,
            $markNotificationReadUseCase
        );
        $controller->markRead();
        break;

    /* ===========================
       NOTIFICATIONS (AJAX API)
    =========================== */

    case 'notification_header_api':
        $controller = new \App\Notification\Presentation\Controller\NotificationApiController(
            $getNotificationsForAdminUseCase,
            $markNotificationReadUseCase,
            $markAllNotificationsReadUseCase
        );
        $controller->adminHeaderData();
        break;

    case 'notification_mark_read':
        $controller = new \App\Notification\Presentation\Controller\NotificationApiController(
            $getNotificationsForAdminUseCase,
            $markNotificationReadUseCase,
            $markAllNotificationsReadUseCase
        );
        $controller->markRead();
        break;

    case 'notification_mark_all_read':
        $controller = new \App\Notification\Presentation\Controller\NotificationApiController(
            $getNotificationsForAdminUseCase,
            $markNotificationReadUseCase,
            $markAllNotificationsReadUseCase
        );
        $controller->markAllRead();
        break;

    case 'admin_notifications':
        $controller = new \App\Notification\Presentation\Controller\NotificationController(
            $getNotificationsForUserUseCase,
            $getNotificationsForAdminUseCase,
            $markNotificationReadUseCase
        );
        $controller->index();
        break;

    default:
        $controller = new CatalogController($catalogPageService, $getRandomCatalog, $catalogItemService);
        $controller->home();
}
