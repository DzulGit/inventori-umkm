<?php

namespace App\Policies;

use App\Models\Barang;
use App\Models\User;

class BarangPolicy
{
    public function viewAny(User $pengguna): bool
    {
        return true;
    }

    public function view(User $pengguna, Barang $barang): bool
    {
        return true;
    }

    public function create(User $pengguna): bool
    {
        return true;
    }

    public function update(User $pengguna, Barang $barang): bool
    {
        return true;
    }

    public function delete(User $pengguna, Barang $barang): bool
    {
        return $pengguna->isOwner();
    }
}