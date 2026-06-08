<?php

namespace App\Shared\Contract;

interface BaseInterface
{
    public function create(array $data);

    public function read(int $id);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function getAll(
        array $criteria = [],
        $limit = null,
        $offset = null
    );

    public function count(array $criteria = []);
}
