<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pemasok;
use App\Policies\BarangPolicy;
use App\Policies\KategoriPolicy;
use App\Policies\PemasokPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Barang::class, BarangPolicy::class);
        Gate::policy(Kategori::class, KategoriPolicy::class);
        Gate::policy(Pemasok::class, PemasokPolicy::class);

        Gate::define('owner-saja', fn ($pengguna) => $pengguna->isOwner());
        Gate::define('lihat-laporan', fn ($pengguna) => $pengguna->isOwner());
    }
}