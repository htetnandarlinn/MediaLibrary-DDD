<?php

namespace App\Shared\Presentation\Controller;

abstract class BaseController
{
    protected function render404(string $message = 'Not Found'): void
    {
        http_response_code(404);
        echo $message;
        exit;
    }

    protected function render500(string $message = 'Internal Server Error'): void
    {
        http_response_code(500);
        echo $message;
        exit;
    }
}
