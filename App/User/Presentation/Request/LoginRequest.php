<?php

namespace App\User\Presentation\Request;

class LoginRequest
{
    public function rules(): array
    {
        return [
            'username_or_email' => ['required' => true],
            'password' => ['required' => true]
        ];
    }
}
