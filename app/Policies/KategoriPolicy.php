<?php

namespace App\Policies;

use App\Models\Kategori;
use App\Models\User;

class KategoriPolicy
{
    public function viewAny(User $pengguna): bool
    {
        return true;
    }

    public function view(User $pengguna, Kategori $kategori): bool
    {
        return true;
    }

    public function create(User $pengguna): bool
    {
        return true;
    }

    public function update(User $pengguna, Kategori $kategori): bool
    {
        return true;
    }

    public function delete(User $pengguna, Kategori $kategori): bool
    {
        return $pengguna->isOwner();
    }
}