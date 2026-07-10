<?php

namespace App\Services;

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as RequestFacade;

class LogAktivitasService
{
    public function catat(?int $penggunaId, string $aktivitas, string $modul, ?string $keterangan = null): void
    {
        try {
            LogAktivitas::create([
                'pengguna_id' => $penggunaId,
                'aktivitas' => $aktivitas,
                'modul' => $modul,
                'keterangan' => $keterangan,
                'ip_address' => RequestFacade::ip(),
            ]);
        } catch (\Throwable $e) {
            // Audit log tidak boleh sampai menggagalkan proses utama.
            // Kalau gagal simpan ke DB, cukup catat di log file sebagai fallback.
            Log::error('Gagal menyimpan log aktivitas', ['error' => $e->getMessage()]);
        }
    }
}