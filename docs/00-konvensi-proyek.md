# Konvensi Proyek

Dokumen ini merangkum aturan yang wajib diikuti setiap kontributor sebelum menambahkan fitur baru.

## Penamaan

| Elemen | Bahasa | Contoh |
|---|---|---|
| Tabel & Kolom | Indonesia | `barang`, `stok_minimal`, `tanggal_transaksi` |
| Model | Indonesia | `Barang`, `Kategori`, `Pemasok`, `Transaksi` |
| Controller | Indonesia | `BarangController` |
| Form Request | Indonesia | `SimpanBarangRequest`, `PerbaruiBarangRequest` |
| Service | Indonesia | `BarangService`, `TransaksiService` |
| Variabel | Indonesia (camelCase) | `stokSaatIni`, `totalHarga` |
| Struktur bawaan Laravel (route method, dsb.) | Inggris | `index`, `store`, `update`, `destroy` |

## Alur Setiap Fitur Baru

1. Analisis kebutuhan & dampak ke tabel lain.
2. Rancang/perbarui ERD bila menyentuh struktur data.
3. Buat migration dengan foreign key, index, dan constraint yang sesuai.
4. Buat Model + relasi Eloquent.
5. Buat Form Request untuk validasi input.
6. Buat Service untuk logic bisnis (transaksi database bila melibatkan banyak tabel).
7. Buat Controller (tipis, hanya memanggil Service).
8. Buat Resource untuk response API.
9. Tambahkan Policy bila perlu otorisasi berbasis kepemilikan/role.
10. Tulis test: autentikasi, otorisasi, validasi, dan logic bisnis.
11. Tulis dokumentasi fitur di `/docs` mengikuti template pada `01-template-fitur.md`.
12. Jalankan `vendor/bin/pint` sebelum commit.
13. Pastikan seluruh test lulus sebelum membuat Pull Request ke `develop`.

## Checklist Keamanan Wajib per Fitur

- [ ] Autentikasi (Sanctum) diterapkan pada route yang memerlukan
- [ ] Otorisasi (Policy/Gate) sesuai role (Owner/Staff)
- [ ] Validasi menggunakan Form Request, bukan validasi manual di Controller
- [ ] Mass assignment protection (`$fillable` / `$guarded`)
- [ ] Query menggunakan Eloquent/Query Builder (parameter binding otomatis)
- [ ] Validasi upload file: ukuran maksimum & MIME type (khusus field `foto`)
- [ ] Audit log untuk aktivitas penting (login, tambah/edit/hapus barang, transaksi)

## Standar Commit

Format: `tipe(scope): deskripsi singkat`

Tipe yang dipakai: `feat`, `fix`, `docs`, `refactor`, `test`, `style`.
