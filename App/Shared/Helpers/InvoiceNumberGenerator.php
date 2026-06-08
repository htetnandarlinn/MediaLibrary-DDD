<?php

namespace App\Shared\Helpers;

class InvoiceNumberGenerator
{
    public static function generate(): string
    {
        return 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
    }
}
