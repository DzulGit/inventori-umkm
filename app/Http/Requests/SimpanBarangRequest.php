<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_barang' => ['required', 'string', 'max:50', 'unique:barang,kode_barang'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'pemasok_id' => ['nullable', 'exists:pemasok,id'],
            'harga_beli' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0', 'gte:harga_beli'],
            'stok' => ['nullable', 'integer', 'min:0'],
            'stok_minimal' => ['nullable', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'harga_jual.gte' => 'Harga jual tidak boleh lebih kecil dari harga beli.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpg, jpeg, atau png.',
            'foto.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}