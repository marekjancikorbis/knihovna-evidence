<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'reader_id' => ['required', 'integer', 'exists:readers,id'],
            'borrowed_at' => ['required', 'date'],
            'due_at' => ['required', 'date', 'after:borrowed_at'],
        ];
    }

    public function attributes(): array
    {
        return [
            'book_id' => 'kniha',
            'reader_id' => 'čtenář',
            'borrowed_at' => 'datum výpůjčky',
            'due_at' => 'termín vrácení',
        ];
    }

    public function messages(): array
    {
        return [
            'due_at.after' => 'Termín vrácení musí být později než datum výpůjčky.',
        ];
    }
}
