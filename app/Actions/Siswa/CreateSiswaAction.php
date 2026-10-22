<?php

namespace App\Actions\Siswa;

use App\Models\Keluarga;
use App\Models\KontakSiswa;
use App\Models\OrangTuaWali;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateSiswaAction
{
    /**
     * Execute student creation with full family, parent, contact, and registration ACID transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data, ?UploadedFile $foto = null): Siswa
    {
        return DB::transaction(function () use ($data, $foto) {
            $fotoPath = null;
            if ($foto instanceof UploadedFile) {
                $fotoPath = $foto->store('siswa_foto', 'public');
            }

            // 1. Simpan Siswa
            $siswa = Siswa::create([
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
                'status_siswa' => $data['status_siswa'] ?? 'aktif',
            ]);

            // 2. Simpan Data Keluarga (1:1)
            if (! empty($data['keluarga'])) {
                Keluarga::create([
                    'kd_siswa' => $siswa->kd_siswa,
                    'no_kk' => $data['keluarga']['no_kk'],
                    'anak_ke' => $data['keluarga']['anak_ke'],
                    'jumlah_saudara' => $data['keluarga']['jumlah_saudara'],
                ]);
            }

            // 3. Simpan Orang Tua / Wali (1:N)
            if (! empty($data['orang_tua'])) {
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

            // 4. Simpan Kontak Siswa (1:N)
            if (! empty($data['kontak'])) {
                foreach ($data['kontak'] as $item) {
                    KontakSiswa::create([
                        'kd_siswa' => $siswa->kd_siswa,
                        'jenis_kontak' => $item['jenis_kontak'],
                        'nomor' => $item['nomor'],
                    ]);
                }
            }

            // 5. Simpan Data Pendaftaran (1:N)
            $pendaftaranData = $data['pendaftaran'] ?? [];
            $noPendaftaran = ! empty($pendaftaranData['no_pendaftaran'])
                ? $pendaftaranData['no_pendaftaran']
                : 'REG-'.date('Y').'-'.$siswa->nis;

            Pendaftaran::create([
                'kd_siswa' => $siswa->kd_siswa,
                'no_pendaftaran' => $noPendaftaran,
                'tgl_daftar' => $pendaftaranData['tgl_daftar'] ?? now()->toDateString(),
                'jalur' => $pendaftaranData['jalur'] ?? 'Reguler',
                'asal_sekolah' => $pendaftaranData['asal_sekolah'] ?? 'SD Asal',
            ]);

            return $siswa->load(['keluarga', 'orangTuaWali', 'kontak', 'pendaftaran', 'agama']);
        });
    }
}
