<?php

namespace App\Suggestion\Domain\Repository;

use App\Suggestion\Domain\Entity\Suggestion;

interface SuggestionRepositoryInterface
{
    public function save(Suggestion $suggestion): bool;
}
