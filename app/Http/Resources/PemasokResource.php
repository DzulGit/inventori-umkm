<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PemasokResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_pemasok' => $this->nama_pemasok,
            'kontak' => $this->kontak,
            'alamat' => $this->alamat,
            'jumlah_barang' => $this->whenCounted('barang'),
            'dibuat_pada' => $this->created_at,
        ];
    }
}