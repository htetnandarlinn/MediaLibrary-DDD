<?php

namespace App\Suggestion\Application\UseCase;

use App\Shared\Exception\ValidationException;
use App\Suggestion\Domain\Entity\Suggestion;
use App\Suggestion\Domain\Repository\SuggestionRepositoryInterface;
use App\Suggestion\Infrastructure\Mail\SuggestionMailer;
use PHPMailer\PHPMailer\PHPMailer;

class SendSuggestionUseCase
{
    public function __construct(
        private SuggestionMailer $mailer,
        private SuggestionRepositoryInterface $repository
    ) {}

    public function execute(array $data): array
    {
        $errors = $this->validate($data);

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }

        $suggestion = Suggestion::fromArray($data);

        $sent = $this->mailer->send($suggestion);

        if (!$sent) {
            return [
                'success' => false,
                'message' => 'Mailer Error: unable to send suggestion.'
            ];
        }

        $this->repository->save($suggestion);

        return [
            'success' => true,
            'message' => 'Suggestion submitted successfully.'
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Name is required.';
        }

        if (empty(trim($data['email'] ?? ''))) {
            $errors['email'] = 'Email is required.';
        } elseif (!PHPMailer::validateAddress($data['email'])) {
            $errors['email'] = 'Invalid email address.';
        }

        if (empty(trim($data['category'] ?? ''))) {
            $errors['category'] = 'Category is required.';
        }

        if (empty(trim($data['title'] ?? ''))) {
            $errors['title'] = 'Title is required.';
        }

        if (!empty($data['address'] ?? '')) {
            $errors['address'] = 'Bad form input.';
        }

        return $errors;
    }
}
