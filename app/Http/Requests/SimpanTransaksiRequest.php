<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanTransaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis' => ['required', 'in:masuk,keluar'],
            'tanggal_transaksi' => ['required', 'date'],
            'keterangan' => ['nullable', 'string'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.barang_id' => ['required', 'exists:barang,id'],
            'detail.*.jumlah' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.in' => 'Jenis transaksi harus "masuk" atau "keluar".',
            'detail.required' => 'Transaksi harus memiliki minimal 1 barang.',
            'detail.*.barang_id.exists' => 'Barang tidak ditemukan.',
            'detail.*.jumlah.min' => 'Jumlah barang minimal 1.',
        ];
    }
}