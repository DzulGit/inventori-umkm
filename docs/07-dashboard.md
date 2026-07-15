# Dashboard

## Tujuan Fitur

Memberi ringkasan cepat kondisi toko: jumlah produk, nilai stok, stok menipis, dan performa penjualan harian/mingguan.

## Cara Kerja

`DashboardController` mendelegasikan seluruh perhitungan ke `DashboardService`. Nilai stok dihitung sebagai `SUM(stok * harga_beli)` seluruh barang (menggambarkan modal yang tertanam di stok, bukan potensi omzet).

## Struktur Database

Tidak ada tabel baru — dashboard mengagregasi data dari `barang` dan `transaksi`.

## Endpoint

| Method | URL | Deskripsi | Autentikasi | Role |
|---|---|---|---|---|
| GET | `/api/dashboard` | Ringkasan dashboard | Ya | Semua |

## Request

Tidak ada parameter.

## Response

**200**
```json
{
  "total_produk": 25,
  "nilai_stok": 4500000.0,
  "jumlah_stok_menipis": 3,
  "penjualan_hari_ini": 320000.0,
  "penjualan_minggu_ini": 1850000.0
}
```

## Validasi

Tidak ada input yang divalidasi.

## Error yang Mungkin Muncul

| Status Code | Kondisi | Pesan |
|---|---|---|
| 401 | Belum login | Unauthenticated |