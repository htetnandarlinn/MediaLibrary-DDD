<?php

namespace App\Invoice\Application\UseCase;

use App\Invoice\Domain\Repository\InvoiceRepositoryInterface;

class GetAllInvoicesUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $repository
    ) {}

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
