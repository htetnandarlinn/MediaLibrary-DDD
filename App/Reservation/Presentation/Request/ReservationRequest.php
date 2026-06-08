<?php

namespace App\Reservation\Presentation\Request;

use App\Shared\Validation\Validator;

class ReservationRequest
{
    public function __construct(
        private Validator $validator = new Validator()
    ) {}

    public function rules(): array
    {
        return [
            'media_id' => [
                'required' => true,
                'integer' => true,
                'min_value' => 1
            ],
            'days' => [
                'required' => true,
                'integer' => true,
                'min_value' => 1
            ]
        ];
    }

    public function validate(array $data): bool
    {
        return $this->validator->validate($data, $this->rules());
    }

    public function errors(): array
    {
        return $this->validator->errors();
    }
}
