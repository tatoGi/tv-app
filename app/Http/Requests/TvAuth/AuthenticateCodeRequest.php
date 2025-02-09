<?php

namespace App\Http\Requests\TvAuth;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:6', 'regex:/^[A-Z0-9]+$/']
        ];
    }
}