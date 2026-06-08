<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Service\CatalogService;

$service = new CatalogService();
$data = $service->getCatalogPage(['page' => 'catalog']);

echo "full total_items: " . ($data['total_items'] ?? '0') . PHP_EOL;
echo "total_pages: " . ($data['total_pages'] ?? '0') . PHP_EOL;
echo "current_page: " . ($data['current_page'] ?? '0') . PHP_EOL;
