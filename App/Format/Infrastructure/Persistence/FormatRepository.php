<?php

namespace App\Format\Infrastructure\Persistence;

use App\Format\Domain\Entity\Format;
use App\Format\Domain\Repository\FormatRepositoryInterface;
use PDO;

class FormatRepository implements FormatRepositoryInterface
{
    public function __construct(
        private PDO $db
    ) {}

    /**
     * Get all formats
     *
     * @return Format[]
     */
    public function getAllFormats(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM formats');
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $formats = [];

        foreach ($rows as $row) {
            $formats[] = $this->mapToEntity($row);
        }

        return $formats;
    }

    /**
     * Find format by ID
     */
    public function findById(int $id): ?Format
    {
        $stmt = $this->db->prepare('SELECT * FROM formats WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToEntity($row) : null;
    }

    /**
     * Save (insert or update)
     */
    public function save(Format $format): bool
    {
        if ($format->getId() === null) {
            return $this->insert($format);
        }

        return $this->update($format);
    }

    /**
     * Delete format
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM formats WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Categories (example query)
     */
    public function getCategories(): array
    {
        $stmt = $this->db->query('SELECT category FROM Media_Types ORDER BY category');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Genres (example query)
     */
    public function getGenres(): array
    {
        $stmt = $this->db->query('SELECT genre FROM Genres ORDER BY genre');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getFormatsByCategory(?string $category = null): array
    {
        if ($category !== null && trim($category) === '') {
            $category = null;
        }

        $stmt = $this->db->prepare(
            'SELECT mt.category, m.format
             FROM Media m
             JOIN Media_Types mt ON m.media_types_id = mt.media_types_id
             WHERE :category IS NULL OR LOWER(mt.category) = LOWER(:category)
               GROUP BY mt.category, m.format
               ORDER BY mt.category, m.format'
        );
        $stmt->bindValue('category', $category, $category === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $formats = [];
        foreach ($results as $row) {
            $formats[$row['category']][] = $row['format'];
        }

        return $formats;
    }

    public function getGenresByCategory(?string $category = null): array
    {
        if ($category !== null && trim($category) === '') {
            $category = null;
        }

        $stmt = $this->db->prepare(
            'SELECT mt.category, g.genre
             FROM Media m
             JOIN Genres g ON m.genre_id = g.genre_id
             JOIN Media_Types mt ON m.media_types_id = mt.media_types_id
             WHERE :category IS NULL OR LOWER(mt.category) = LOWER(:category)
               GROUP BY mt.category, g.genre
               ORDER BY mt.category, g.genre'
        );
        $stmt->bindValue('category', $category, $category === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $genres = [];
        foreach ($results as $row) {
            $genres[$row['category']][] = $row['genre'];
        }

        return $genres;
    }

    // ---------------------------
    // PRIVATE HELPERS
    // ---------------------------

    private function insert(Format $format): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO formats (name, slug, description, created_at, updated_at)
            VALUES (:name, :slug, :description, :created_at, :updated_at)
        ');

        return $stmt->execute([
            'name' => $format->getName(),
            'slug' => $format->getSlug(),
            'description' => $format->getDescription(),
            'created_at' => $format->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $format->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ]);
    }

    private function update(Format $format): bool
    {
        $stmt = $this->db->prepare('
            UPDATE formats
            SET name = :name,
                slug = :slug,
                description = :description,
                updated_at = :updated_at
            WHERE id = :id
        ');

        return $stmt->execute([
            'id' => $format->getId(),
            'name' => $format->getName(),
            'slug' => $format->getSlug(),
            'description' => $format->getDescription(),
            'updated_at' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Map DB row → Domain Entity
     */
    private function mapToEntity(array $row): Format
    {
        return new Format(
            id: (int) $row['id'],
            name: $row['name'],
            slug: $row['slug'] ?? null,
            description: $row['description'] ?? null,
            createdAt: isset($row['created_at'])
                ? new \DateTimeImmutable($row['created_at'])
                : null,
            updatedAt: isset($row['updated_at'])
                ? new \DateTimeImmutable($row['updated_at'])
                : null
        );
    }
}
