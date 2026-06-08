<?php

namespace App\Invoice\Application\UseCase;

use App\Invoice\Domain\Repository\InvoiceRepositoryInterface;

class GetInvoiceByIdUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?array
    {
        return $this->repository->findById($id);
    }
}
