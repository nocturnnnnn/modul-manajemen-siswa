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
     * @param  array{kd_siswa: int, sekolah_tujuan: string, tgl_pindah: string, alasan: string}  $data
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

            $siswa = Siswa::findOrFail($data['kd_siswa']);
            $siswa->update(['status_siswa' => 'pindah']);

            return $pindah;
        });
    }
}
