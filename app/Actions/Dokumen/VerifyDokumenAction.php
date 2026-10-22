<?php

namespace App\Actions\Dokumen;

use App\Models\DokumenSiswa;

class VerifyDokumenAction
{
    /**
     * Handle document verification status update.
     */
    public function execute(DokumenSiswa $dokumen, string $status, ?string $catatan = null): DokumenSiswa
    {
        $dokumen->update([
            'status_verifikasi' => $status,
            'catatan_verifikasi' => $status === 'terverifikasi' ? ($catatan ?: null) : $catatan,
        ]);

        return $dokumen->fresh();
    }
}
