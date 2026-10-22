<?php

namespace App\Actions\Kelulusan;

use App\Models\Kelulusan;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProsesKelulusanAction
{
    /**
     * Process grade 9 student graduation, assign no ijazah, and set status to 'lulus'.
     *
     * @param  array{kd_siswa: int, kd_tahun: int, tgl_lulus: string, no_ijazah?: string|null}  $data
     */
    public function execute(array $data): Kelulusan
    {
        $siswa = Siswa::with('kelulusan')->findOrFail($data['kd_siswa']);

        $latestKenaikan = $siswa->kenaikanKelas()->latest('kd_kenaikan')->first();
        $isGrade9 = $latestKenaikan && $latestKenaikan->kelasTujuan && $latestKenaikan->kelasTujuan->tingkat === 9;

        if (! $isGrade9 && ! $siswa->kelulusan) {
            throw new InvalidArgumentException('Hanya siswa tingkat 9 yang dapat diluluskan.');
        }

        return DB::transaction(function () use ($siswa, $data) {
            $kelulusan = Kelulusan::updateOrCreate(
                ['kd_siswa' => $data['kd_siswa']],
                [
                    'kd_tahun' => $data['kd_tahun'],
                    'tgl_lulus' => $data['tgl_lulus'],
                    'no_ijazah' => $data['no_ijazah'] ?? null,
                ]
            );

            if ($siswa->status_siswa !== 'alumni') {
                $siswa->update(['status_siswa' => 'lulus']);
            }

            return $kelulusan;
        });
    }
}
