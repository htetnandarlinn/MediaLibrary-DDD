<?php

namespace App\Suggestion\Presentation\Controller;

use App\Format\Application\UseCase\GetCategoriesUseCase;
use App\Format\Application\UseCase\GetSuggestionFormatsUseCase;
use App\Format\Application\UseCase\GetSuggestionGenresUseCase;
use App\Shared\Exception\ValidationException;
use App\Shared\Validation\Validator;
use App\Suggestion\Application\UseCase\SendSuggestionUseCase;
use App\Suggestion\Presentation\Request\SuggestionRequest;

class SuggestController
{
    public function __construct(
        private SendSuggestionUseCase $sendSuggestionUseCase,
        private Validator $validator,
        private SuggestionRequest $request,
        private GetCategoriesUseCase $categoriesUseCase,
        private GetSuggestionFormatsUseCase $formatsUseCase,
        private GetSuggestionGenresUseCase $genresUseCase
    ) {}

    public function index(): void
    {
        $pageTitle = 'Suggest a media item';
        $section = 'suggest';
        $hideSearch = true;

        $name = '';
        $email = '';
        $category = '';
        $title = '';
        $format = '';
        $genre = '';
        $year = '';
        $details = '';
        $errors = [];
        $error_message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $format = trim($_POST['format'] ?? '');
            $genre = trim($_POST['genre'] ?? '');
            $year = trim($_POST['year'] ?? '');
            $details = trim($_POST['details'] ?? '');

            $isValid = $this->validator->validate(
                $_POST,
                $this->request->rules()
            );

            if (!$isValid) {
                $errors = $this->validator->errors();
            } else {
                try {
                    $result = $this->sendSuggestionUseCase->execute([
                        'name' => $name,
                        'email' => $email,
                        'category' => $category,
                        'title' => $title,
                        'format' => $format,
                        'genre' => $genre,
                        'year' => $year,
                        'details' => $details,
                        'address' => $_POST['address'] ?? ''
                    ]);

                    if ($result['success']) {
                        header('Location: ' . BASE_URL . '/Public/index.php?page=suggest&status=thanks');
                        exit;
                    }

                    $error_message = $result['message'];
                } catch (ValidationException $e) {
                    $errors = $e->errors();
                    $error_message = $e->getMessage();
                }
            }
        }

        $categories = $this->categoriesUseCase->execute();
        $formats = $this->formatsUseCase->execute();
        $genres = $this->genresUseCase->execute();

        require BASE_PATH . '/App/Suggestion/Presentation/View/suggest.php';
    }
}
