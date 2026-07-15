# Barang

## Tujuan Fitur

Mengelola master data produk/barang yang dijual, termasuk stok, harga beli/jual, dan foto produk.

## Cara Kerja

`BarangController` mendelegasikan logic ke `BarangService`. Field `stok` **tidak bisa diubah langsung** lewat endpoint update — perubahan stok hanya melalui modul Transaksi (barang masuk/keluar), supaya seluruh perubahan stok punya jejak transaksi yang jelas.

## Struktur Database

Tabel `barang` (soft delete):
- `kode_barang` (unique)
- `nama_barang` (index)
- `kategori_id` (FK → kategori, restrict)
- `pemasok_id` (FK → pemasok, nullable, null on delete)
- `harga_beli`, `harga_jual` (decimal 15,2)
- `stok`, `stok_minimal` (integer)
- `deskripsi` (nullable)
- `foto` (path file, nullable)

## Endpoint

| Method | URL | Deskripsi | Autentikasi | Role |
|---|---|---|---|---|
| GET | `/api/barang` | Daftar barang (filter: kategori_id, pemasok_id, cari) | Ya | Semua |
| POST | `/api/barang` | Tambah barang | Ya | Semua |
| GET | `/api/barang/{id}` | Detail barang | Ya | Semua |
| PUT | `/api/barang/{id}` | Perbarui barang (tanpa stok) | Ya | Semua |
| DELETE | `/api/barang/{id}` | Hapus barang (soft delete) | Ya | **Owner saja** |
| GET | `/api/barang/stok-menipis` | Daftar barang dengan stok ≤ stok_minimal | Ya | Semua |

## Request

**POST** (multipart/form-data jika menyertakan foto)
```json
{
  "kode_barang": "BRG-0001",
  "nama_barang": "Beras 5kg",
  "kategori_id": 1,
  "pemasok_id": 1,
  "harga_beli": 55000,
  "harga_jual": 65000,
  "stok": 40,
  "stok_minimal": 10,
  "deskripsi": "Beras kualitas premium"
}
```

## Response

**200/201**
```json
{
  "data": {
    "id": 1,
    "kode_barang": "BRG-0001",
    "nama_barang": "Beras 5kg",
    "kategori": { "id": 1, "nama_kategori": "Sembako" },
    "pemasok": { "id": 1, "nama_pemasok": "CV Sumber Rejeki" },
    "harga_beli": "55000.00",
    "harga_jual": "65000.00",
    "stok": 40,
    "stok_minimal": 10,
    "stok_menipis": false,
    "deskripsi": "Beras kualitas premium",
    "foto": null,
    "dibuat_pada": "2026-07-01T08:00:00.000000Z"
  }
}
```

## Validasi

- `kode_barang`: wajib, unik
- `nama_barang`: wajib, maksimal 255 karakter
- `kategori_id`: wajib, harus ada di tabel kategori
- `pemasok_id`: opsional, harus ada di tabel pemasok jika diisi
- `harga_beli`: wajib, numerik, minimal 0
- `harga_jual`: wajib, numerik, **harus ≥ harga_beli**
- `foto`: opsional, gambar (jpg/jpeg/png), maksimal 2MB

## Error yang Mungkin Muncul

| Status Code | Kondisi | Pesan |
|---|---|---|
| 422 | Kode barang duplikat | "Kode barang sudah digunakan." |
| 422 | Harga jual < harga beli | "Harga jual tidak boleh lebih kecil dari harga beli." |
| 422 | Foto bukan gambar/terlalu besar | "Format gambar harus jpg, jpeg, atau png." / "Ukuran gambar maksimal 2MB." |
| 403 | Staff mencoba menghapus barang | Forbidden |
| 404 | Barang tidak ditemukan | Not Found |