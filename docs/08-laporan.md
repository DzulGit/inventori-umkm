# Laporan

## Tujuan Fitur

Menyediakan laporan stok dan penjualan yang bisa difilter dan diexport ke Excel. **Khusus role Owner** — mempertimbangkan data ini bersifat sensitif (nilai stok, omzet).

## Cara Kerja

`LaporanController` mendelegasikan pengambilan data ke `LaporanService`, dan export ke `LaporanStokExport` / `LaporanPenjualanExport` (menggunakan package `maatwebsite/excel`). Seluruh route laporan dibatasi dengan middleware `can:lihat-laporan` (Gate khusus Owner).

## Struktur Database

Tidak ada tabel baru — laporan mengagregasi data dari `barang` dan `transaksi` + `detail_transaksi`.

## Endpoint

| Method | URL | Deskripsi | Autentikasi | Role |
|---|---|---|---|---|
| GET | `/api/laporan/stok` | Laporan stok (filter: kategori_id, pemasok_id, stok_menipis_saja) | Ya | **Owner saja** |
| GET | `/api/laporan/stok/export` | Export laporan stok ke Excel | Ya | **Owner saja** |
| GET | `/api/laporan/penjualan` | Laporan penjualan (filter: tanggal_dari, tanggal_sampai, kategori_id) | Ya | **Owner saja** |
| GET | `/api/laporan/penjualan/export` | Export laporan penjualan ke Excel (1 baris = 1 barang terjual) | Ya | **Owner saja** |

## Request

**GET `/api/laporan/penjualan?tanggal_dari=2026-07-01&tanggal_sampai=2026-07-09`**

## Response

**200** — array data barang/transaksi sesuai filter (lihat struktur di `BarangResource`/`TransaksiResource`).

**Export** — response file `.xlsx` (download).

## Validasi

Tidak ada validasi ketat pada filter (semua opsional); filter yang tidak valid diabaikan secara aman oleh query builder.

## Error yang Mungkin Muncul

| Status Code | Kondisi | Pesan |
|---|---|---|
| 403 | Staff mencoba mengakses laporan | Forbidden |