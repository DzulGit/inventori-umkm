<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanPemasokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_pemasok' => ['required', 'string', 'max:255'],
            'kontak' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}