# Template Dokumentasi Fitur

Salin format ini untuk setiap fitur baru dan simpan sebagai `/docs/NN-nama-fitur.md`.

## Tujuan Fitur

Jelaskan masalah bisnis yang diselesaikan fitur ini.

## Cara Kerja

Jelaskan alur singkat: Controller mana yang dipanggil, Service apa yang menjalankan logic, dan Model apa yang terlibat.

## Struktur Database

Sebutkan tabel yang terlibat, kolom penting, relasi (foreign key), dan constraint.

## Endpoint

| Method | URL | Deskripsi | Autentikasi | Role |
|---|---|---|---|---|
| GET | `/api/...` | ... | Ya/Tidak | Owner/Staff |

## Request

Contoh payload dan penjelasan tiap field.

## Response

Contoh response sukses (status code, struktur JSON).

## Validasi

Daftar aturan validasi per field beserta pesan error yang mungkin muncul.

## Error yang Mungkin Muncul

| Status Code | Kondisi | Pesan |
|---|---|---|
| 422 | Validasi gagal | ... |
| 403 | Tidak memiliki izin | ... |
| 404 | Data tidak ditemukan | ... |
