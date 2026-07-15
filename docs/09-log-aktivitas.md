# Log Aktivitas (Audit Log)

## Tujuan Fitur

Mencatat aktivitas penting pengguna untuk keperluan audit: login, logout, ganti password, tambah/edit/hapus barang, transaksi masuk/keluar. Membantu investigasi jika ada perubahan data yang mencurigakan.

## Cara Kerja

Setiap Service (Auth, Barang, Transaksi) memanggil `LogAktivitasService::catat()` setelah aksi penting berhasil dijalankan. Penyimpanan log dibungkus try-catch — jika penyimpanan log gagal, proses utama **tidak ikut gagal** (audit log bersifat pelengkap, bukan syarat mutlak transaksi berhasil), tapi kegagalannya tetap dicatat ke log file sebagai fallback.

## Struktur Database

Tabel `log_aktivitas`:
- `pengguna_id` (FK → users, nullable, null on delete)
- `aktivitas` (string, mis. `login`, `tambah_barang`, `transaksi_keluar`)
- `modul` (string, mis. `autentikasi`, `barang`, `transaksi`)
- `keterangan` (text, nullable — detail tambahan mis. nama barang/kode transaksi)
- `ip_address` (string, nullable)

## Endpoint

Belum ada endpoint API untuk melihat log aktivitas (data ini untuk keperluan internal/audit, belum termasuk MVP). Bisa diakses langsung lewat database atau `php artisan tinker` untuk saat ini.

## Request / Response

Tidak berlaku — fitur ini bekerja otomatis di background, bukan endpoint yang dipanggil langsung.

## Validasi

Tidak ada input dari pengguna secara langsung.

## Error yang Mungkin Muncul

| Kondisi | Penanganan |
|---|---|
| Penyimpanan log gagal (mis. DB down) | Dicatat ke log file sebagai fallback, proses utama tetap lanjut |