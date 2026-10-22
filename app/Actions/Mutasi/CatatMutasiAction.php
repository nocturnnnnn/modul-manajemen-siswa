<?php

namespace App\Actions\Mutasi;

use App\Models\Mutasi;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class CatatMutasiAction
{
    /**
     * Record a student mutation (Incoming, Outgoing, or Internal) and update status.
     *
     * @param  array{kd_siswa: int, jenis_mutasi: string, tgl_mutasi: string, no_surat: string}  $data
     */
    public function execute(array $data): Mutasi
    {
        return DB::transaction(function () use ($data) {
            $mutasi = Mutasi::create([
                'kd_siswa' => $data['kd_siswa'],
                'jenis_mutasi' => $data['jenis_mutasi'],
                'tgl_mutasi' => $data['tgl_mutasi'],
                'no_surat' => $data['no_surat'],
            ]);

            $siswa = Siswa::findOrFail($data['kd_siswa']);

            if ($data['jenis_mutasi'] === 'Keluar') {
                $siswa->update(['status_siswa' => 'mutasi']);
            } elseif ($data['jenis_mutasi'] === 'Masuk' || $data['jenis_mutasi'] === 'Internal') {
                $siswa->update(['status_siswa' => 'aktif']);
            }

            return $mutasi;
        });
    }
}
