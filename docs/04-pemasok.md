# Pemasok

## Tujuan Fitur

Mengelola data pemasok/supplier barang.

## Cara Kerja

`PemasokController` mendelegasikan logic ke `PemasokService`. Berbeda dari Kategori, penghapusan pemasok **tidak diblokir** meski masih ada barang terkait — foreign key `barang.pemasok_id` bersifat nullable dengan `nullOnDelete`, sehingga barang tetap ada tapi kehilangan referensi pemasoknya.

## Struktur Database

Tabel `pemasok` (soft delete):
- `nama_pemasok`
- `kontak` (nullable)
- `alamat` (nullable)

Relasi: `pemasok` (1) — (banyak) `barang`, foreign key `barang.pemasok_id` nullable, `nullOnDelete`.

## Endpoint

| Method | URL | Deskripsi | Autentikasi | Role |
|---|---|---|---|---|
| GET | `/api/pemasok` | Daftar pemasok (paginated) | Ya | Semua |
| POST | `/api/pemasok` | Tambah pemasok | Ya | Semua |
| GET | `/api/pemasok/{id}` | Detail pemasok + jumlah barang | Ya | Semua |
| PUT | `/api/pemasok/{id}` | Perbarui pemasok | Ya | Semua |
| DELETE | `/api/pemasok/{id}` | Hapus pemasok (soft delete) | Ya | **Owner saja** |

## Request

**POST/PUT**
```json
{
  "nama_pemasok": "CV Sumber Rejeki",
  "kontak": "081234567890",
  "alamat": "Jl. Pati - Kudus No. 10"
}
```

## Response

**200/201**
```json
{
  "data": {
    "id": 1,
    "nama_pemasok": "CV Sumber Rejeki",
    "kontak": "081234567890",
    "alamat": "Jl. Pati - Kudus No. 10",
    "jumlah_barang": 5,
    "dibuat_pada": "2026-07-01T08:00:00.000000Z"
  }
}
```

## Validasi

- `nama_pemasok`: wajib, string, maksimal 255 karakter
- `kontak`: opsional, string, maksimal 50 karakter
- `alamat`: opsional, string

## Error yang Mungkin Muncul

| Status Code | Kondisi | Pesan |
|---|---|---|
| 422 | Field wajib kosong | Pesan validasi standar Laravel |
| 403 | Staff mencoba menghapus pemasok | Forbidden |
| 404 | Pemasok tidak ditemukan | Not Found |