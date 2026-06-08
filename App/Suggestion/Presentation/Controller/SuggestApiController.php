<?php

namespace App\Suggestion\Presentation\Controller;

use App\Shared\Exception\ValidationException;
use App\Suggestion\Application\UseCase\SendSuggestionUseCase;

class SuggestApiController
{
    public function __construct(
        private SendSuggestionUseCase $sendSuggestionUseCase
    ) {}

    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Use POST to submit a suggestion.'
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            return;
        }

        $requestData = $this->getRequestData();

        try {
            $result = $this->sendSuggestionUseCase->execute($requestData);

            http_response_code($result['success'] ? 200 : 422);
            echo json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (ValidationException $e) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    private function getRequestData(): array
    {
        $contentType = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '')[0] ?? ''));

        if ($contentType === 'application/json') {
            $rawBody = file_get_contents('php://input');
            $payload = json_decode($rawBody, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON request body.'
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                exit;
            }

            return $payload;
        }

        return $_POST;
    }
}
