<?php

namespace App\Actions\Dokumen;

use App\Models\DokumenSiswa;
use Illuminate\Http\UploadedFile;

class UploadDokumenAction
{
    /**
     * Handle document upload and persistence.
     *
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, UploadedFile $file): DokumenSiswa
    {
        $filePath = $file->store('dokumen_siswa', 'public');

        return DokumenSiswa::create([
            'kd_siswa' => $data['kd_siswa'],
            'kd_jenis' => $data['kd_jenis'],
            'nomor_dokumen' => $data['nomor_dokumen'] ?? null,
            'file_path' => $filePath,
            'status_verifikasi' => 'belum',
            'catatan_verifikasi' => null,
            'tgl_upload' => now(),
        ]);
    }
}
