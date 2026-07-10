<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemasok extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pemasok';

    protected $fillable = ['nama_pemasok', 'kontak', 'alamat'];

    public function barang()
    {
        return $this->hasMany(Barang::class);
    }
}
