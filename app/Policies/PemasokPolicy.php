<?php

namespace App\Policies;

use App\Models\Pemasok;
use App\Models\User;

class PemasokPolicy
{
    public function viewAny(User $pengguna): bool
    {
        return true;
    }

    public function view(User $pengguna, Pemasok $pemasok): bool
    {
        return true;
    }

    public function create(User $pengguna): bool
    {
        return true;
    }

    public function update(User $pengguna, Pemasok $pemasok): bool
    {
        return true;
    }

    public function delete(User $pengguna, Pemasok $pemasok): bool
    {
        return $pengguna->isOwner();
    }
}