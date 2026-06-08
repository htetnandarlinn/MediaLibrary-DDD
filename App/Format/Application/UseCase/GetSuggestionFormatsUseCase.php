<?php

namespace App\Format\Application\UseCase;

use App\Format\Domain\Repository\FormatRepositoryInterface;

class GetSuggestionFormatsUseCase
{
    public function __construct(
        private FormatRepositoryInterface $repository
    ) {}

    public function execute(?string $category = null): array
    {
        return $this->repository->getFormatsByCategory($category);
    }
}
