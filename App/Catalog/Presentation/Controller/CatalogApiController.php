<?php

namespace App\Catalog\Presentation\Controller;

use App\Catalog\Application\UseCase\GetCatalogItemUseCase;
use App\Catalog\Application\UseCase\GetCatalogPageUseCase;
use App\Catalog\Domain\Entity\Catalog;

class CatalogApiController
{
    public function __construct(
        private GetCatalogPageUseCase $getCatalogPageUseCase,
        private GetCatalogItemUseCase $getCatalogItemUseCase
    ) {}

    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id !== null && $id !== false) {
            $item = $this->getCatalogItemUseCase->execute($id);
            if (empty($item)) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Item not found',
                    'id' => $id
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                exit;
            }

            echo json_encode([
                'success' => true,
                'item' => $item
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit;
        }

        $data = $this->getCatalogPageUseCase->execute($_GET);
        if (!is_array($data)) {
            $data = [];
        }

        $catalog = $data['catalog'] ?? [];
        if (is_array($catalog)) {
            $data['catalog'] = array_map(
                fn($item) => $item instanceof Catalog ? $item->toArray() : $item,
                $catalog
            );
            $catalog = $data['catalog'];
        }

        $response = [
            'success' => true,
            'count' => is_array($catalog) ? count($catalog) : 0,
            'empty' => empty($catalog),
            'data' => $data,
        ];

        http_response_code(200);
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
