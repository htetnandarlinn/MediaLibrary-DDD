<?php

namespace App\User\Application\DTO;

class ResponseDTO
{
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data = null
    ) {}
}
