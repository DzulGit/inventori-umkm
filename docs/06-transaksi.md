# Transaksi

## Tujuan Fitur

Mencatat pergerakan stok barang: transaksi masuk (restock dari pemasok) dan transaksi keluar (penjualan ke pelanggan).

## Cara Kerja

`TransaksiController` mendelegasikan logic ke `TransaksiService`. Setiap transaksi diproses dalam satu **database transaction** (`DB::transaction`):

1. Untuk setiap baris detail, baris `barang` dikunci (`lockForUpdate`) untuk mencegah race condition saat 2 transaksi mengakses barang yang sama secara bersamaan.
2. Transaksi `keluar` divalidasi stok cukup — jika tidak, seluruh transaksi dibatalkan (rollback), termasuk baris lain yang sudah lolos validasi.
3. Harga disnapshot ke `detail_transaksi.harga_satuan` (harga_beli untuk masuk, harga_jual untuk keluar) — histori transaksi lama tidak berubah meski harga barang di-update kemudian.
4. Stok barang diperbarui (`+` untuk masuk, `-` untuk keluar).
5. Kode transaksi otomatis dibuat: `MSK-YYYYMMDD-0001` atau `KLR-YYYYMMDD-0001`.

## Struktur Database

Tabel `transaksi`:
- `kode_transaksi` (unique)
- `jenis` (enum: masuk, keluar)
- `tanggal_transaksi`
- `pengguna_id` (FK → users, restrict)
- `total_harga`
- `keterangan` (nullable)

Tabel `detail_transaksi`:
- `transaksi_id` (FK → transaksi, cascade on delete)
- `barang_id` (FK → barang, restrict)
- `jumlah`, `harga_satuan`, `subtotal`

## Endpoint

| Method | URL | Deskripsi | Autentikasi | Role |
|---|---|---|---|---|
| GET | `/api/transaksi` | Daftar transaksi (filter: tanggal, jenis, kategori, pemasok) | Ya | Semua |
| POST | `/api/transaksi` | Buat transaksi baru | Ya | Semua |

## Request

**POST**
```json
{
  "jenis": "keluar",
  "tanggal_transaksi": "2026-07-09 10:00:00",
  "keterangan": "Penjualan reguler",
  "detail": [
    { "barang_id": 1, "jumlah": 2 },
    { "barang_id": 3, "jumlah": 5 }
  ]
}
```

## Response

**201**
```json
{
  "data": {
    "id": 10,
    "kode_transaksi": "KLR-20260709-0001",
    "jenis": "keluar",
    "tanggal_transaksi": "2026-07-09T10:00:00.000000Z",
    "pengguna": { "id": 1, "nama": "Pemilik Toko" },
    "total_harga": "155000.00",
    "keterangan": "Penjualan reguler",
    "detail": [
      { "barang_id": 1, "nama_barang": "Beras 5kg", "jumlah": 2, "harga_satuan": "65000.00", "subtotal": "130000.00" },
      { "barang_id": 3, "nama_barang": "Teh Botol 350ml", "jumlah": 5, "harga_satuan": "4500.00", "subtotal": "22500.00" }
    ]
  }
}
```

## Validasi

- `jenis`: wajib, harus `masuk` atau `keluar`
- `tanggal_transaksi`: wajib, format tanggal valid
- `detail`: wajib, array, minimal 1 baris
- `detail.*.barang_id`: wajib, harus ada di tabel barang
- `detail.*.jumlah`: wajib, integer, minimal 1

## Error yang Mungkin Muncul

| Status Code | Kondisi | Pesan |
|---|---|---|
| 422 | Stok tidak cukup untuk transaksi keluar | "Stok {nama barang} tidak cukup. Sisa stok: {n}." |
| 422 | Detail kosong | "Transaksi harus memiliki minimal 1 barang." |
| 422 | Barang tidak ditemukan | "Barang tidak ditemukan." |