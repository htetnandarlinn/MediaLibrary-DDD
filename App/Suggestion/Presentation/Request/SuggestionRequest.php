<?php

namespace App\Suggestion\Presentation\Request;

class SuggestionRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required' => true],
            'email' => ['required' => true, 'email' => true],
            'category' => ['required' => true],
            'title' => ['required' => true],
            'format' => [],
            'genre' => [],
            'year' => [],
            'details' => []
        ];
    }
}
