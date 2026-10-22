<?php

namespace App\Actions\Kelulusan;

use App\Models\Alumni;
use App\Models\Kelulusan;
use Illuminate\Support\Facades\DB;

class UpdateAlumniAction
{
    /**
     * Record or update alumni directory details and set status to 'alumni'.
     *
     * @param  array{kd_lulus: int, kegiatan_lanjutan: string, instansi: string, kontak?: string|null}  $data
     */
    public function execute(array $data): Alumni
    {
        return DB::transaction(function () use ($data) {
            $alumni = Alumni::updateOrCreate(
                ['kd_lulus' => $data['kd_lulus']],
                [
                    'kegiatan_lanjutan' => $data['kegiatan_lanjutan'],
                    'instansi' => $data['instansi'],
                    'kontak' => $data['kontak'] ?? null,
                ]
            );

            $kelulusan = Kelulusan::with('siswa')->findOrFail($data['kd_lulus']);
            if ($kelulusan->siswa) {
                $kelulusan->siswa->update(['status_siswa' => 'alumni']);
            }

            return $alumni;
        });
    }
}
