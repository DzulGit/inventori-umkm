<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GantiPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
