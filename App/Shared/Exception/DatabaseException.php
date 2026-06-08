<?php

namespace App\Shared\Exception;

use Exception;

class DatabaseException extends Exception
{
    public function __construct(
        string $message = 'Database operation failed.'
    ) {
        parent::__construct($message);
    }
}
