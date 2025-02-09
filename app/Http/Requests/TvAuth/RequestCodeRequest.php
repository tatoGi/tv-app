<?php

namespace App\Http\Requests\TvAuth;

use Illuminate\Foundation\Http\FormRequest;

class RequestCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}