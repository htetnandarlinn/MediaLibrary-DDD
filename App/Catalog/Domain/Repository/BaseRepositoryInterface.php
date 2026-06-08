<?php

namespace App\Shared\Repository;

interface BaseRepositoryInterface
{
    public function findById(int $id);
}
