<?php

namespace App\Actions\Mutasi;

use App\Models\PindahSekolah;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class CatatPindahSekolahAction
{
    /**
     * Record a student school transfer and update student status to 'pindah'.
     *
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): PindahSekolah
    {
        return DB::transaction(function () use ($data) {
            $pindah = PindahSekolah::create([
                'kd_siswa' => $data['kd_siswa'],
                'sekolah_tujuan' => $data['sekolah_tujuan'],
                'tgl_pindah' => $data['tgl_pindah'],
                'alasan' => $data['alasan'],
            ]);

            $siswa = Siswa::where('kd_siswa', (int) $data['kd_siswa'])->firstOrFail();
            $siswa->update(['status_siswa' => 'pindah']);

            return $pindah;
        });
    }
}
