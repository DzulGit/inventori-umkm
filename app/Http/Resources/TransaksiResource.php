<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_transaksi' => $this->kode_transaksi,
            'jenis' => $this->jenis,
            'tanggal_transaksi' => $this->tanggal_transaksi,
            'pengguna' => $this->pengguna ? [
                'id' => $this->pengguna->id,
                'nama' => $this->pengguna->nama ?? $this->pengguna->name,
            ] : null,
            'total_harga' => $this->total_harga,
            'keterangan' => $this->keterangan,
            'detail' => $this->detail->map(fn ($d) => [
                'barang_id' => $d->barang_id,
                'nama_barang' => $d->barang->nama_barang ?? null,
                'jumlah' => $d->jumlah,
                'harga_satuan' => $d->harga_satuan,
                'subtotal' => $d->subtotal,
            ]),
        ];
    }
}