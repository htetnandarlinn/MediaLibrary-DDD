<?php

namespace App\Request;

class RegisterUserRequest
{
    public function rules(): array
    {
        return [

            'username' => [
                'required' => true,
                'min' => 3,
                'max' => 50,
            ],

            'email' => [
                'required' => true,
                'email' => true,
            ],

            'password' => [
                'required' => true,
                'min' => 8,
            ],

            'confirm_password' => [
                'required' => true,
                'match' => 'password',
            ],
        ];
    }
}