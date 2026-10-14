<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Allow ISBN entered with hyphens/spaces ("978-80-...") – store the bare 13 characters.
        if (is_string($this->isbn)) {
            $this->merge(['isbn' => str_replace(['-', ' '], '', $this->isbn)]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => [
                'required',
                'string',
                'size:13',
                Rule::unique('books', 'isbn')->ignore($this->route('book')),
            ],
            'available_copies' => ['required', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'název',
            'author' => 'autor',
            'isbn' => 'ISBN',
            'available_copies' => 'počet dostupných výtisků',
        ];
    }
}
