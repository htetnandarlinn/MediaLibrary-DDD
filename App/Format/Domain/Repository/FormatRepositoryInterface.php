<?php

namespace App\Format\Domain\Repository;

use App\Format\Domain\Entity\Format;

/**
 * Format Repository Contract (DDD)
 *
 * This defines what the infrastructure layer must implement.
 * No SQL, no PDO, only business-level operations.
 */
interface FormatRepositoryInterface
{
    /**
     * Get all formats
     *
     * @return array<Format>
     */
    public function getAllFormats(): array;

    /**
     * Get all categories related to formats
     *
     * @return array
     */
    public function getCategories(): array;

    /**
     * Get all genres related to formats
     *
     * @return array
     */
    public function getGenres(): array;

    public function getFormatsByCategory(?string $category = null): array;

    public function getGenresByCategory(?string $category = null): array;

    /**
     * Find a format by ID
     */
    public function findById(int $id): ?Format;

    /**
     * Save a format (create or update)
     */
    public function save(Format $format): bool;

    /**
     * Delete a format by ID
     */
    public function delete(int $id): bool;
}
