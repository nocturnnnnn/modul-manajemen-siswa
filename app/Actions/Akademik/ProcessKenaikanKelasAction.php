<?php

namespace App\Actions\Akademik;

use App\Models\Kelas;
use App\Models\KenaikanKelas;
use InvalidArgumentException;

class ProcessKenaikanKelasAction
{
    /**
     * Process promoting a student to the next grade level.
     */
    public function execute(int $kdSiswa, int $kdTahun, int $kdKelasAsal, int $kdKelasTujuan): KenaikanKelas
    {
        $kelasAsal = Kelas::findOrFail($kdKelasAsal);
        $kelasTujuan = Kelas::findOrFail($kdKelasTujuan);

        if ($kelasAsal->tingkat >= 9) {
            throw new InvalidArgumentException('Siswa tingkat 9 tidak dapat dinaikkan kelas, harus melalui modul kelulusan.');
        }

        if ($kelasTujuan->tingkat !== $kelasAsal->tingkat + 1) {
            throw new InvalidArgumentException("Kelas tujuan kenaikan kelas harus satu tingkat lebih tinggi ({$kelasAsal->tingkat} -> ".($kelasAsal->tingkat + 1).').');
        }

        return KenaikanKelas::updateOrCreate(
            [
                'kd_siswa' => $kdSiswa,
                'kd_tahun' => $kdTahun,
            ],
            [
                'kd_kelas_asal' => $kdKelasAsal,
                'kd_kelas_tujuan' => $kdKelasTujuan,
                'status' => 'naik',
            ]
        );
    }
}
