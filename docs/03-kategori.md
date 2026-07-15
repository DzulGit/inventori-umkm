# Kategori

## Tujuan Fitur

Mengelola pengelompokan barang (misal: Sembako, Minuman, Alat Tulis) agar barang mudah difilter dan dilaporkan.

## Cara Kerja

`KategoriController` mendelegasikan logic ke `KategoriService`. Penghapusan kategori diblokir jika masih ada barang yang terkait (mencegah data barang menjadi tidak konsisten).

## Struktur Database

Tabel `kategori`:
- `nama_kategori` (unique)
- `deskripsi` (nullable)

Relasi: `kategori` (1) — (banyak) `barang`, foreign key `barang.kategori_id` bersifat `restrict` (kategori tidak bisa dihapus paksa lewat DB selama masih dipakai).

## Endpoint

| Method | URL | Deskripsi | Autentikasi | Role |
|---|---|---|---|---|
| GET | `/api/kategori` | Daftar kategori (paginated) | Ya | Semua |
| POST | `/api/kategori` | Tambah kategori | Ya | Semua |
| GET | `/api/kategori/{id}` | Detail kategori + jumlah barang | Ya | Semua |
| PUT | `/api/kategori/{id}` | Perbarui kategori | Ya | Semua |
| DELETE | `/api/kategori/{id}` | Hapus kategori | Ya | **Owner saja** |

## Request

**POST/PUT**
```json
{
  "nama_kategori": "Sembako",
  "deskripsi": "Kebutuhan pokok sehari-hari"
}
```

## Response

**200/201**
```json
{
  "data": {
    "id": 1,
    "nama_kategori": "Sembako",
    "deskripsi": "Kebutuhan pokok sehari-hari",
    "jumlah_barang": 12,
    "dibuat_pada": "2026-07-01T08:00:00.000000Z"
  }
}
```

## Validasi

- `nama_kategori`: wajib, string, maksimal 255 karakter, unik (kecuali dirinya sendiri saat update)
- `deskripsi`: opsional, string

## Error yang Mungkin Muncul

| Status Code | Kondisi | Pesan |
|---|---|---|
| 422 | Nama kategori duplikat | "Nama kategori sudah digunakan." |
| 422 | Hapus kategori yang masih punya barang | "Kategori tidak bisa dihapus karena masih memiliki barang terkait." |
| 403 | Staff mencoba menghapus kategori | Forbidden |
| 404 | Kategori tidak ditemukan | Not Found |