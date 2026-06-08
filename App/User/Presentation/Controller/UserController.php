<?php

namespace App\User\Presentation\Controller;

use App\Shared\Exception\DatabaseException;
use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\ValidationException;
use App\Shared\Presentation\Controller\BaseController;
use App\Shared\Validation\Validator;
use App\User\Application\UseCase\AuthenticateUserUseCase;
use App\User\Application\UseCase\RegisterUserUseCase;
use App\User\Presentation\Request\LoginRequest;
use App\User\Presentation\Request\RegisterUserRequest;


//User Controller DDD structure
class UserController extends BaseController
{
    private ?AuthenticateUserUseCase $authenticateUserUseCase;
    private ?RegisterUserUseCase $registerUserUseCase;

    public function __construct(
        ?AuthenticateUserUseCase $authenticateUserUseCase,
        ?RegisterUserUseCase $registerUserUseCase
    ) {
        $this->authenticateUserUseCase = $authenticateUserUseCase;
        $this->registerUserUseCase = $registerUserUseCase;
    }

    public function login(
        LoginRequest $loginRequest,
        Validator $validator
    ): void {
        $pageTitle = 'Login';
        $section = 'login';
        $hideSearch = true;

        $usernameOrEmail = '';
        $errors = [];
        $errorMessage = null;
        $successMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usernameOrEmail = trim($_POST['username_or_email'] ?? '');

            $isValid = $validator->validate(
                $_POST,
                $loginRequest->rules()
            );

            if (!$isValid) {
                $errors = $validator->errors();
            } else {
                try {
                    if ($this->authenticateUserUseCase === null) {
                        $errors['error_message'] = 'Authentication service unavailable. Please try again later.';
                    } else {
                        $user = $this->authenticateUserUseCase->execute(
                            trim($_POST['username_or_email']),
                            $_POST['password']
                        );

                        $_SESSION['user'] = $user->toArray();

                        $redirectPage = strtolower($user->role ?? '') === 'admin'
                            ? 'reservation_admin'
                            : 'catalog';

                        header(
                            'Location: '
                            . BASE_URL
                            . '/Public/index.php?page='
                            . $redirectPage
                        );

                        exit;
                    }
                } catch (ValidationException $e) {
                    $errors = $e->errors();
                } catch (NotFoundException $e) {
                    $this->render404(
                        $e->getMessage()
                    );
                } catch (DatabaseException $e) {
                    $this->render500(
                        'Login failed because of a database error.'
                    );
                } catch (\Throwable $e) {
                    error_log(
                        '[Login error] ' . $e->getMessage() . "\n" . $e->getTraceAsString()
                    );

                    $errors['error_message'] =
                        'An unexpected error occurred. Please try again later.';
                }
            }
        }

        require BASE_PATH . '/view/login.php';
    }

    public function register(
        RegisterUserRequest $request,
        Validator $validator
    ): void {
        $pageTitle = 'Register';
        $section = 'register';
        $hideSearch = true;

        $username = '';
        $email = '';
        $successMessage = null;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username =
                trim($_POST['username'] ?? '');

            $email =
                trim($_POST['email'] ?? '');

            $isValid = $validator->validate(
                $_POST,
                $request->rules()
            );

            if (!$isValid) {
                $errors = $validator->errors();
            } else {
                try {
                    if ($this->registerUserUseCase === null) {
                        $errors['error_message'] =
                            'Registration service is unavailable. Please try again later.';
                    } else {
                        $response =
                            $this->registerUserUseCase->execute([
                                'username' => $username,
                                'email' => $email,
                                'password' =>
                                    $_POST['password'] ?? '',
                                'confirm_password' =>
                                    $_POST['confirm_password'] ?? ''
                            ]);

                        $successMessage =
                            $response->message;

                        $username = '';
                        $email = '';
                    }
                } catch (ValidationException $e) {
                    $errors = $e->errors();
                } catch (NotFoundException $e) {
                    $this->render404(
                        $e->getMessage()
                    );
                } catch (DatabaseException $e) {
                    error_log('[Register error] ' . $e->getMessage());
                    $this->render500(
                        'Registration failed because of a database error.'
                    );
                } catch (\Throwable $e) {
                    error_log(
                        '[Register error] ' . $e->getMessage() . "\n" . $e->getTraceAsString()
                    );
                    $this->render500(
                        'Unexpected error occurred.'
                    );
                }
            }
        }

        require BASE_PATH . '/view/register.php';
    }

    public function logout(): void
    {
        session_unset();

        session_destroy();

        header(
            'Location: '
            . BASE_URL
            . '/Public/index.php?page=index'
        );

        exit;
    }
}
