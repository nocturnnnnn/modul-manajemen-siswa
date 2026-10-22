<?php

namespace Database\Seeders;

use App\Models\Alumni;
use App\Models\DokumenSiswa;
use App\Models\Keluarga;
use App\Models\Kelulusan;
use App\Models\KenaikanKelas;
use App\Models\KontakSiswa;
use App\Models\Mutasi;
use App\Models\OrangTuaWali;
use App\Models\Pendaftaran;
use App\Models\PindahSekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SiswaSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Siswa Aktif Kelas 7 (Baru Terdaftar)
        $siswa1 = Siswa::updateOrCreate(
            ['nis' => '202607001'],
            [
                'nisn' => '0091234567',
                'nik' => '3307010101090001',
                'nm_siswa' => 'Daniel Pratama Christian',
                'tempat_lahir' => 'Wonosobo',
                'tgl_lahir' => '2012-05-14',
                'jenis_kelamin' => 'Laki-laki',
                'kd_agama' => 1, // Kristen
                'alamat' => 'Jl. Bhayangkara No. 12, Wonosobo',
                'foto' => null,
                'status_siswa' => 'aktif',
            ]
        );

        Keluarga::updateOrCreate(
            ['kd_siswa' => $siswa1->kd_siswa],
            [
                'no_kk' => '3307011005120001',
                'anak_ke' => 1,
                'jumlah_saudara' => 2,
            ]
        );

        OrangTuaWali::updateOrCreate(
            ['kd_siswa' => $siswa1->kd_siswa, 'hubungan' => 'ayah'],
            [
                'nm_ortu' => 'Bambang Christian',
                'nik' => '3307010101750001',
                'pekerjaan' => 'Wiraswasta',
                'pendidikan' => 'S1',
                'no_hp' => '081234567890',
            ]
        );

        OrangTuaWali::updateOrCreate(
            ['kd_siswa' => $siswa1->kd_siswa, 'hubungan' => 'ibu'],
            [
                'nm_ortu' => 'Maria Kristina',
                'nik' => '3307010101780002',
                'pekerjaan' => 'Guru',
                'pendidikan' => 'S1',
                'no_hp' => '081234567891',
            ]
        );

        KontakSiswa::updateOrCreate(
            ['kd_siswa' => $siswa1->kd_siswa, 'jenis_kontak' => 'WhatsApp'],
            ['nomor' => '081234567892']
        );

        Pendaftaran::updateOrCreate(
            ['no_pendaftaran' => 'PPDB-2026-001'],
            [
                'kd_siswa' => $siswa1->kd_siswa,
                'tgl_daftar' => '2026-06-15',
                'jalur' => 'Prestasi',
                'asal_sekolah' => 'SD Kristen 1 Wonosobo',
            ]
        );

        DokumenSiswa::updateOrCreate(
            ['kd_siswa' => $siswa1->kd_siswa, 'kd_jenis' => 1],
            [
                'nomor_dokumen' => '3307011005120001',
                'file_path' => 'dokumen/sample_kk_daniel.pdf',
                'status_verifikasi' => 'terverifikasi',
                'catatan_verifikasi' => 'Kartu keluarga sah dan terverifikasi.',
                'tgl_upload' => Carbon::now(),
            ]
        );

        // 2. Siswa Aktif Kelas 8 (Mengalami Kenaikan Kelas dari 7A ke 8A)
        $siswa2 = Siswa::updateOrCreate(
            ['nis' => '202507002'],
            [
                'nisn' => '0089876543',
                'nik' => '3307020202080002',
                'nm_siswa' => 'Grace Yohana Wijaya',
                'tempat_lahir' => 'Semarang',
                'tgl_lahir' => '2011-08-20',
                'jenis_kelamin' => 'Perempuan',
                'kd_agama' => 1, // Kristen
                'alamat' => 'Jl. Mayor Kaslam No. 45, Wonosobo',
                'foto' => null,
                'status_siswa' => 'aktif',
            ]
        );

        Keluarga::updateOrCreate(
            ['kd_siswa' => $siswa2->kd_siswa],
            [
                'no_kk' => '3307021008110002',
                'anak_ke' => 2,
                'jumlah_saudara' => 3,
            ]
        );

        OrangTuaWali::updateOrCreate(
            ['kd_siswa' => $siswa2->kd_siswa, 'hubungan' => 'ayah'],
            [
                'nm_ortu' => 'Hendra Wijaya',
                'nik' => '3307020202720001',
                'pekerjaan' => 'Pegawai Swasta',
                'pendidikan' => 'D3',
                'no_hp' => '082134567801',
            ]
        );

        KontakSiswa::updateOrCreate(
            ['kd_siswa' => $siswa2->kd_siswa, 'jenis_kontak' => 'HP'],
            ['nomor' => '082134567802']
        );

        KenaikanKelas::updateOrCreate(
            ['kd_siswa' => $siswa2->kd_siswa, 'kd_tahun' => 2],
            [
                'kd_kelas_asal' => 1, // 7A
                'kd_kelas_tujuan' => 4, // 8A
                'status' => 'naik',
            ]
        );

        // 3. Siswa Lulus & Alumni (Tingkat 9 yang telah tamat)
        $siswa3 = Siswa::updateOrCreate(
            ['nis' => '202307003'],
            [
                'nisn' => '0071122334',
                'nik' => '3307030303070003',
                'nm_siswa' => 'Samuel Setiawan',
                'tempat_lahir' => 'Wonosobo',
                'tgl_lahir' => '2010-02-10',
                'jenis_kelamin' => 'Laki-laki',
                'kd_agama' => 2, // Katolik
                'alamat' => 'Jl. Pasukan Ronggolawe No. 8, Wonosobo',
                'foto' => null,
                'status_siswa' => 'alumni',
            ]
        );

        $kelulusan = Kelulusan::updateOrCreate(
            ['kd_siswa' => $siswa3->kd_siswa],
            [
                'kd_tahun' => 2, // 2025/2026 Genap
                'tgl_lulus' => '2026-06-10',
                'no_ijazah' => 'DN-03/D-SMP/26/000123',
            ]
        );

        Alumni::updateOrCreate(
            ['kd_lulus' => $kelulusan->kd_lulus],
            [
                'kegiatan_lanjutan' => 'Melanjutkan SMA',
                'instansi' => 'SMA Kristen 1 Wonosobo',
                'kontak' => '085643219876',
            ]
        );

        // 4. Siswa Pindah Sekolah
        $siswa4 = Siswa::updateOrCreate(
            ['nis' => '202507004'],
            [
                'nisn' => '0085544332',
                'nik' => '3307040404080004',
                'nm_siswa' => 'Ruth Anggraini',
                'tempat_lahir' => 'Purwokerto',
                'tgl_lahir' => '2011-11-12',
                'jenis_kelamin' => 'Perempuan',
                'kd_agama' => 1, // Kristen
                'alamat' => 'Jl. Sabuk Alu No. 20, Wonosobo',
                'foto' => null,
                'status_siswa' => 'pindah',
            ]
        );

        PindahSekolah::updateOrCreate(
            ['kd_siswa' => $siswa4->kd_siswa],
            [
                'sekolah_tujuan' => 'SMP Kristen Purwokerto',
                'tgl_pindah' => '2026-01-10',
                'alasan' => 'Mengikuti perpindahan tugas orang tua ke Purwokerto',
            ]
        );

        Mutasi::updateOrCreate(
            ['kd_siswa' => $siswa4->kd_siswa, 'no_surat' => '421.3/089/SMPK1/2026'],
            [
                'jenis_mutasi' => 'Keluar',
                'tgl_mutasi' => '2026-01-10',
            ]
        );
    }
}
