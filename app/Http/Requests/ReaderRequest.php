<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReaderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('readers', 'email')->ignore($this->route('reader')),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'jméno',
            'email' => 'e-mail',
        ];
    }
}
