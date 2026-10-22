<?php

namespace App\Actions\Akademik;

use App\Models\Kelas;
use App\Models\KenaikanKelas;
use InvalidArgumentException;

class ProcessTinggalKelasAction
{
    /**
     * Process retaining a student at the same grade level.
     */
    public function execute(int $kdSiswa, int $kdTahun, int $kdKelasAsal, int $kdKelasTujuan): KenaikanKelas
    {
        $kelasAsal = Kelas::findOrFail($kdKelasAsal);
        $kelasTujuan = Kelas::findOrFail($kdKelasTujuan);

        if ($kelasTujuan->tingkat !== $kelasAsal->tingkat) {
            throw new InvalidArgumentException("Kelas tujuan tinggal kelas harus berada pada tingkat yang sama ({$kelasAsal->tingkat}).");
        }

        return KenaikanKelas::updateOrCreate(
            [
                'kd_siswa' => $kdSiswa,
                'kd_tahun' => $kdTahun,
            ],
            [
                'kd_kelas_asal' => $kdKelasAsal,
                'kd_kelas_tujuan' => $kdKelasTujuan,
                'status' => 'tinggal',
            ]
        );
    }
}
