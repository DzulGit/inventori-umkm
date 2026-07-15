# Autentikasi

## Tujuan Fitur

Mengelola akses pengguna ke sistem: login, logout, ganti password, dan melihat profil. Membedakan hak akses antara role `owner` dan `staff`.

## Cara Kerja

`AuthController` menerima request dan mendelegasikan seluruh logic ke `AuthService`. Autentikasi menggunakan Laravel Sanctum (token-based). Setiap aksi (login, logout, ganti password) tercatat di tabel `log_aktivitas` melalui `LogAktivitasService`.

## Struktur Database

Tabel `users` (bawaan Laravel + tambahan):
- `role` (enum: `owner`, `staff`) — default `staff`
- `deleted_at` — soft delete

## Endpoint

| Method | URL | Deskripsi | Autentikasi | Role |
|---|---|---|---|---|
| POST | `/api/login` | Login, mengembalikan token | Tidak | Semua |
| POST | `/api/logout` | Logout, menghapus token aktif | Ya | Semua |
| GET | `/api/profil` | Melihat data profil sendiri | Ya | Semua |
| PUT | `/api/ganti-password` | Mengubah password sendiri | Ya | Semua |

## Request

**POST `/api/login`**
```json
{
  "email": "owner@umkm.local",
  "password": "password"
}
```

**PUT `/api/ganti-password`**
```json
{
  "password_lama": "password",
  "password_baru": "passwordBaru123",
  "password_baru_confirmation": "passwordBaru123"
}
```

## Response

**POST `/api/login`** — 200 OK
```json
{
  "pengguna": { "id": 1, "name": "Pemilik Toko", "email": "owner@umkm.local", "role": "owner" },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

## Validasi

- `email`: wajib, format email valid
- `password`: wajib
- `password_lama`: wajib, harus cocok dengan password saat ini
- `password_baru`: wajib, minimal 8 karakter, harus ada `password_baru_confirmation` yang sama

## Error yang Mungkin Muncul

| Status Code | Kondisi | Pesan |
|---|---|---|
| 422 | Email/password salah saat login | "Email atau password salah." |
| 422 | Password lama tidak cocok | "Password lama tidak sesuai." |
| 401 | Token tidak ada/invalid saat akses endpoint terproteksi | Unauthenticated |
| 429 | Login dicoba lebih dari 5 kali dalam 1 menit | Too Many Requests |