<?php

namespace App\Actions\Siswa;

use App\Models\KontakSiswa;
use App\Models\OrangTuaWali;
use App\Models\Siswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateSiswaAction
{
    /**
     * Execute student update with relations synchronization in a single ACID transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public function execute(Siswa $siswa, array $data, ?UploadedFile $foto = null): Siswa
    {
        return DB::transaction(function () use ($siswa, $data, $foto) {
            $fotoPath = $siswa->foto;

            if ($foto instanceof UploadedFile) {
                if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                    Storage::disk('public')->delete($siswa->foto);
                }
                $fotoPath = $foto->store('siswa_foto', 'public');
            }

            // 1. Update Siswa
            $siswa->update([
                'nis' => $data['nis'],
                'nisn' => $data['nisn'],
                'nik' => $data['nik'],
                'nm_siswa' => $data['nm_siswa'],
                'tempat_lahir' => $data['tempat_lahir'],
                'tgl_lahir' => $data['tgl_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'kd_agama' => $data['kd_agama'],
                'alamat' => $data['alamat'],
                'foto' => $fotoPath,
                'status_siswa' => $data['status_siswa'] ?? $siswa->status_siswa,
            ]);

            // 2. Update / Create Keluarga
            if (! empty($data['keluarga'])) {
                $siswa->keluarga()->updateOrCreate(
                    ['kd_siswa' => $siswa->kd_siswa],
                    [
                        'no_kk' => $data['keluarga']['no_kk'],
                        'anak_ke' => $data['keluarga']['anak_ke'],
                        'jumlah_saudara' => $data['keluarga']['jumlah_saudara'],
                    ]
                );
            }

            // 3. Sync Orang Tua / Wali
            if (isset($data['orang_tua'])) {
                $siswa->orangTuaWali()->delete();
                foreach ($data['orang_tua'] as $ortu) {
                    OrangTuaWali::create([
                        'kd_siswa' => $siswa->kd_siswa,
                        'hubungan' => $ortu['hubungan'],
                        'nm_ortu' => $ortu['nm_ortu'],
                        'nik' => $ortu['nik'] ?? null,
                        'pekerjaan' => $ortu['pekerjaan'] ?? null,
                        'pendidikan' => $ortu['pendidikan'] ?? null,
                        'no_hp' => $ortu['no_hp'] ?? null,
                    ]);
                }
            }

            // 4. Sync Kontak Siswa
            if (isset($data['kontak'])) {
                $siswa->kontak()->delete();
                foreach ($data['kontak'] as $item) {
                    KontakSiswa::create([
                        'kd_siswa' => $siswa->kd_siswa,
                        'jenis_kontak' => $item['jenis_kontak'],
                        'nomor' => $item['nomor'],
                    ]);
                }
            }

            // 5. Update Pendaftaran if present
            if (! empty($data['pendaftaran'])) {
                $pendaftaran = $siswa->pendaftaran()->first();
                if ($pendaftaran) {
                    $pendaftaran->update([
                        'no_pendaftaran' => $data['pendaftaran']['no_pendaftaran'] ?? $pendaftaran->no_pendaftaran,
                        'tgl_daftar' => $data['pendaftaran']['tgl_daftar'] ?? $pendaftaran->tgl_daftar,
                        'jalur' => $data['pendaftaran']['jalur'] ?? $pendaftaran->jalur,
                        'asal_sekolah' => $data['pendaftaran']['asal_sekolah'] ?? $pendaftaran->asal_sekolah,
                    ]);
                }
            }

            return $siswa->fresh(['keluarga', 'orangTuaWali', 'kontak', 'pendaftaran', 'agama']);
        });
    }
}
