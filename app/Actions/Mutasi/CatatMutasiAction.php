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
     * @param  array<string, mixed>  $data
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

            $siswa = Siswa::where('kd_siswa', (int) $data['kd_siswa'])->firstOrFail();

            if ($data['jenis_mutasi'] === 'Keluar') {
                $siswa->update(['status_siswa' => 'mutasi']);
            } elseif ($data['jenis_mutasi'] === 'Masuk' || $data['jenis_mutasi'] === 'Internal') {
                $siswa->update(['status_siswa' => 'aktif']);
            }

            return $mutasi;
        });
    }
}
