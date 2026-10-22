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
     * @param  array<string, mixed>  $data
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

            $kelulusan = Kelulusan::with('siswa')->where('kd_lulus', (int) $data['kd_lulus'])->firstOrFail();
            if ($kelulusan->siswa) {
                $kelulusan->siswa->update(['status_siswa' => 'alumni']);
            }

            return $alumni;
        });
    }
}
