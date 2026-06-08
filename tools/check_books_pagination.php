<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Service\CatalogService;

$service = new CatalogService();
$data = $service->getCatalogPage(['page' => 'catalog', 'cat' => 'books']);

echo "total_items: " . ($data['total_items'] ?? '0') . PHP_EOL;
echo "total_pages: " . ($data['total_pages'] ?? '0') . PHP_EOL;
echo "current_page: " . ($data['current_page'] ?? '0') . PHP_EOL;
echo "items_on_page: " . count($data['catalog']) . PHP_EOL;

// print first few item titles for verification
foreach (($data['catalog'] ?? []) as $i => $item) {
    $title = $item['title'] ?? ($item['name'] ?? 'NO_TITLE');
    echo ($i + 1) . ". " . $title . PHP_EOL;
}
