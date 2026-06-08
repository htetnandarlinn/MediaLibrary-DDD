<?php

define('BASE_PATH', dirname(__DIR__));

define('BASE_URL', '/MediaLibrary-MVC-Test');

require_once BASE_PATH . '/vendor/autoload.php';

use App\Catalog\Application\UseCase\GetCatalogItemUseCase;
use App\Catalog\Application\UseCase\GetCatalogPageUseCase;
use App\Catalog\Application\UseCase\GetRandomCatalogUseCase;
use App\Catalog\Infrastructure\Persistence\CatalogRepository;
use App\Format\Application\UseCase\GetCategoriesUseCase;
use App\Format\Application\UseCase\GetSuggestionFormatsUseCase;
use App\Format\Application\UseCase\GetSuggestionGenresUseCase;
use App\Format\Infrastructure\Persistence\FormatRepository;
use App\Invoice\Application\UseCase\GetAllInvoicesUseCase;
use App\Invoice\Application\UseCase\GetInvoiceByIdUseCase;
use App\Invoice\Application\UseCase\GetInvoicesForUserUseCase;
use App\Invoice\Infrastructure\InvoiceRepository;
use App\Invoice\Presentation\Controller\InvoiceController;
use App\Notification\Application\UseCase\CreateNotificationUseCase;
use App\Notification\Infrastructure\Persistence\NotificationRepository;
use App\Reservation\Application\Service\ReservationPriceCalculator;
use App\Notification\Application\UseCase\GetNotificationsForAdminUseCase;
use App\Notification\Application\UseCase\GetNotificationsForUserUseCase;
use App\Notification\Application\UseCase\MarkAllNotificationsReadUseCase;
use App\Notification\Application\UseCase\MarkNotificationReadUseCase;
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
use App\Reservation\Infrastructure\Persistence\ReservationRepository;
use App\Reservation\Presentation\Controller\ReservationController;
use App\Reservation\Presentation\Request\ReservationRequest;
use App\Shared\Database\Database;
use App\Shared\Exception\DatabaseException;
use App\Shared\Validation\Validator;
use App\Suggestion\Application\UseCase\SendSuggestionUseCase;
use App\Suggestion\Infrastructure\Mail\SuggestionMailer;
use App\Suggestion\Infrastructure\Persistence\SuggestRepository;
use App\Suggestion\Presentation\Request\SuggestionRequest;
use App\User\Application\UseCase\AuthenticateUserUseCase;
use App\User\Application\UseCase\RegisterUserUseCase;
use App\User\Infrastructure\Persistence\UserRepository;
use App\User\Presentation\Request\LoginRequest;
use App\User\Presentation\Request\RegisterUserRequest;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

session_start();

/* DATABASE */
try {
    $db = Database::getConnection();
} catch (DatabaseException $e) {
    http_response_code(500);
    $pageTitle = 'Server Error';
    $section = '';
    $hideSearch = true;
    require BASE_PATH . '/view/errors/500.php';
    exit;
}

/* REPOSITORIES */
$catalogRepo = new CatalogRepository($db);
$catalogItemService = new GetCatalogItemUseCase($catalogRepo);
$catalogPageService = new GetCatalogPageUseCase($catalogRepo);
$getRandomCatalog = new GetRandomCatalogUseCase($catalogRepo);

$formatRepo = new FormatRepository($db);
$getCategories = new GetCategoriesUseCase($formatRepo);
$getSuggestionFormats = new GetSuggestionFormatsUseCase($formatRepo);
$getSuggestionGenres = new GetSuggestionGenresUseCase($formatRepo);

$userRepo = new UserRepository($db);
$authenticateUserUseCase = new AuthenticateUserUseCase($userRepo);
$registerUserUseCase = new RegisterUserUseCase($userRepo);
$loginUserRequest = new LoginRequest();  // In a real app, you'd want to sanitize this input
$registerUserRequest = new RegisterUserRequest();  // In a real app, you'd want to sanitize this
$suggestionMailer = new SuggestionMailer();
$suggestionRepo = new SuggestRepository($db);
$sendSuggestionUseCase = new SendSuggestionUseCase($suggestionMailer, $suggestionRepo);
$suggestionRequest = new SuggestionRequest();
$validator = new Validator();

$reservationRepo = new ReservationRepository($db);
$reservationPriceCalculator = new ReservationPriceCalculator();
$notificationRepo = new NotificationRepository($db);
$invoiceRepo = new InvoiceRepository($db);
$createReservationUseCase = new CreateReservationUseCase($reservationRepo, $catalogRepo, $reservationPriceCalculator, $notificationRepo);
$getUserReservationsUseCase = new GetUserReservationsUseCase($reservationRepo);
$getPendingReservationsUseCase = new GetPendingReservationsUseCase($reservationRepo);
$getReservationStatsUseCase = new GetReservationStatsUseCase($reservationRepo);
$getLatestApprovedReservationsUseCase = new GetLatestApprovedReservationsUseCase($reservationRepo);
$getReservationsByStatusUseCase = new GetReservationsByStatusUseCase($reservationRepo);
$getAllReservationsUseCase = new GetAllReservationsUseCase($reservationRepo);
$approveReservationUseCase = new ApproveReservationUseCase($reservationRepo, $notificationRepo);
$rejectReservationUseCase = new RejectReservationUseCase($reservationRepo, $notificationRepo);
$getNotificationsForUserUseCase = new GetNotificationsForUserUseCase($notificationRepo);
$getNotificationsForAdminUseCase = new GetNotificationsForAdminUseCase($notificationRepo);
$markNotificationReadUseCase = new MarkNotificationReadUseCase($notificationRepo);
$markAllNotificationsReadUseCase = new MarkAllNotificationsReadUseCase($notificationRepo);
$createNotificationUseCase = new CreateNotificationUseCase($notificationRepo);
$completeReservationPaymentUseCase = new CompleteReservationPaymentUseCase($reservationRepo, $invoiceRepo, $notificationRepo);
$getInvoicesForUserUseCase = new GetInvoicesForUserUseCase($invoiceRepo);
$getInvoiceByIdUseCase = new GetInvoiceByIdUseCase($invoiceRepo);
$getAllInvoicesUseCase = new GetAllInvoicesUseCase($invoiceRepo);
$reservationRequest = new ReservationRequest();
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = $uri;

if (defined('BASE_URL') && BASE_URL !== '' && str_starts_with($route, BASE_URL)) {
    $route = substr($route, strlen(BASE_URL));
    if ($route === '') {
        $route = '/';
    }
}

/* ROUTER LOAD */
$page = $_GET['page'] ?? 'home';

if (str_starts_with($page, 'api/')) {
    require BASE_PATH . '/routes/api.php';
} else {
    require BASE_PATH . '/routes/web.php';
}
