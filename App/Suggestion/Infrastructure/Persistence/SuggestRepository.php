<?php

namespace App\Suggestion\Infrastructure\Persistence;

use App\Suggestion\Domain\Entity\Suggestion;
use App\Suggestion\Domain\Repository\SuggestionRepositoryInterface;
use PDO;
use PDOException;

class SuggestRepository implements SuggestionRepositoryInterface
{
    public function __construct(
        private ?PDO $db = null
    ) {}

    public function save(Suggestion $suggestion): bool
    {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->prepare(
                    'INSERT INTO suggestions (name, email, category, title, format, genre, year, details, submitted_at) VALUES (:name, :email, :category, :title, :format, :genre, :year, :details, :submitted_at)'
                );

                return $stmt->execute([
                    'name' => $suggestion->getName(),
                    'email' => $suggestion->getEmail(),
                    'category' => $suggestion->getCategory(),
                    'title' => $suggestion->getTitle(),
                    'format' => $suggestion->getFormat(),
                    'genre' => $suggestion->getGenre(),
                    'year' => $suggestion->getYear(),
                    'details' => $suggestion->getDetails(),
                    'submitted_at' => $suggestion->getSubmittedAt()?->format('Y-m-d H:i:s'),
                ]);
            } catch (PDOException $e) {
                error_log('[SuggestRepository] save failed: ' . $e->getMessage());
            }
        }

        $logFile = sys_get_temp_dir() . '/suggestion_log.json';

        $existing = [];
        if (file_exists($logFile)) {
            $json = file_get_contents($logFile);
            $existing = json_decode($json, true) ?? [];
        }

        $existing[] = $suggestion->toArray();

        return file_put_contents($logFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }
}
