<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BarangResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_barang' => $this->kode_barang,
            'nama_barang' => $this->nama_barang,
            'kategori' => $this->kategori ? [
                'id' => $this->kategori->id,
                'nama_kategori' => $this->kategori->nama_kategori,
            ] : null,
            'pemasok' => $this->pemasok ? [
                'id' => $this->pemasok->id,
                'nama_pemasok' => $this->pemasok->nama_pemasok,
            ] : null,
            'harga_beli' => $this->harga_beli,
            'harga_jual' => $this->harga_jual,
            'stok' => $this->stok,
            'stok_minimal' => $this->stok_minimal,
            'stok_menipis' => $this->stokMenipis(),
            'deskripsi' => $this->deskripsi,
            'foto' => $this->foto ? asset('storage/'.$this->foto) : null,
            'dibuat_pada' => $this->created_at,
        ];
    }
}