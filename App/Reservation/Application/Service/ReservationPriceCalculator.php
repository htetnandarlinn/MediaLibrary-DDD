<?php

namespace App\Reservation\Application\Service;

use App\Catalog\Domain\Entity\Catalog;

class ReservationPriceCalculator
{
    public function calculateAmountCents(array $itemData, int $days): int
    {
        $category = strtolower(trim($itemData['category'] ?? ''));
        $days = max(1, $days);

        switch ($category) {
            case 'books':
                $dailyRate = 200;
                break;
            case 'movies':
                $dailyRate = 300;
                break;
            case 'music':
                $dailyRate = 150;
                break;
            default:
                $dailyRate = 250;
                break;
        }

        return $dailyRate * $days;
    }

    public function buildDescription(array $itemData, int $days): string
    {
        $title = trim((string) ($itemData['title'] ?? 'Library Reservation'));
        $dayLabel = $days === 1 ? 'day' : 'days';

        return sprintf('%s reservation for %d %s', $title, $days, $dayLabel);
    }
}
