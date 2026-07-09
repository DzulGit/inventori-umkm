# Sistem Manajemen Inventori UMKM

Backend Laravel untuk sistem manajemen inventori UMKM. Frontend belum menjadi prioritas — seluruh logika bisnis, database, validasi, keamanan, dan pengujian diselesaikan terlebih dahulu.

## Teknologi

- Laravel 12
- PHP 8.4
- PostgreSQL (Supabase sebagai database platform)
- Laravel Sanctum (autentikasi API)
- Pest / PHPUnit
- Laravel Pint

## Arsitektur

```
Controller → Service → Model
```

Controller hanya mengatur alur (thin controller). Seluruh logic bisnis berada di `app/Services`. Jangan menambahkan Repository Pattern, Action Pattern, atau Domain Layer kecuali benar-benar dibutuhkan.

## Struktur Folder

```
app/
├── Http/
│   ├── Controllers/   # Alur request-response saja
│   ├── Requests/      # Form Request untuk validasi (mis. SimpanBarangRequest)
│   └── Resources/     # Resource Response (transformasi output API)
├── Models/            # Eloquent Model (Barang, Kategori, Pemasok, Transaksi, dst.)
├── Policies/          # Otorisasi per model
├── Services/          # Logic bisnis (mis. BarangService, TransaksiService)
└── Providers/

database/
├── migrations/
├── factories/
└── seeders/

docs/                  # Dokumentasi setiap fitur
```

## Setup Lokal

1. Salin `.env.example` menjadi `.env` dan sesuaikan kredensial database (PostgreSQL/Supabase).
2. Jalankan:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate
   ```
3. Jalankan server:
   ```bash
   php artisan serve
   ```
4. Jalankan test:
   ```bash
   php artisan test
   ```

> **Catatan penting:** Folder `vendor/` belum terisi di lingkungan pembuatan proyek ini karena environment sandbox tidak memiliki akses ke `packagist.org` (hanya domain seperti github.com, npmjs.org, pypi.org yang diizinkan). Jalankan `composer install` di mesin lokal Anda yang memiliki akses internet penuh untuk mengunduh dependency.

## Git Workflow

```
main
 └── develop
      └── feature/nama-fitur
```

Merge hanya melalui Pull Request. Gunakan Conventional Commit, contoh:

```
feat(barang): tambah CRUD barang
fix(transaksi): perbaiki validasi stok
docs(database): tambah ERD
```

## Prioritas Pengembangan

1. Keamanan
2. Konsistensi kode
3. Kemudahan maintenance
4. Keterbacaan kode
5. Kemudahan testing
6. Dokumentasi
7. Performa
8. Pengembangan fitur

Lihat `/docs` untuk dokumentasi tiap fitur (tujuan, cara kerja, struktur database, endpoint, request/response, validasi, dan error).
