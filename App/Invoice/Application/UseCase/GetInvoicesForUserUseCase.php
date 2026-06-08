<?php

namespace App\Invoice\Application\UseCase;

use App\Invoice\Domain\Repository\InvoiceRepositoryInterface;

class GetInvoicesForUserUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $repository
    ) {}

    public function execute(int $userId): array
    {
        return $this->repository->getByUserId($userId);
    }
}
