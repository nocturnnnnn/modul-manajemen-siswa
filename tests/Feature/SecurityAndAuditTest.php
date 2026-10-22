<?php

use App\Models\Kelulusan;
use App\Models\Siswa;
use Database\Seeders\AgamaSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\SiswaSampleSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed([
        AgamaSeeder::class,
        JenisDokumenSeeder::class,
        TahunAjaranSeeder::class,
        KelasSeeder::class,
        SiswaSampleSeeder::class,
    ]);
});

it('sanitizes and safely handles sql injection payloads in search queries', function () {
    $maliciousQueries = [
        "' OR '1'='1",
        "'; DROP TABLE siswa; --",
        "1' UNION SELECT * FROM users --",
        "<script>alert('xss')</script>",
        "admin'--",
    ];

    foreach ($maliciousQueries as $query) {
        // Search in Siswa
        $this->get(route('siswa.index', ['search' => $query]))->assertOk();

        // Search in Dokumen
        $this->get(route('dokumen.index', ['search' => $query]))->assertOk();

        // Search in Mutasi
        $this->get(route('mutasi.index', ['search' => $query]))->assertOk();

        // Search in Kelulusan
        $this->get(route('kelulusan.index', ['search' => $query]))->assertOk();

        // Search in Laporan
        $this->get(route('laporan.index', ['search' => $query]))->assertOk();
    }

    // Verify database was not altered
    expect(Siswa::count())->toBeGreaterThan(0);
});

it('rejects arbitrary or executable file uploads for student documents', function () {
    $siswa = Siswa::first();
    $dangerousFiles = [
        UploadedFile::fake()->create('malicious.php', 100, 'application/x-php'),
        UploadedFile::fake()->create('trojan.exe', 100, 'application/x-msdownload'),
        UploadedFile::fake()->create('script.sh', 50, 'text/x-shellscript'),
    ];

    foreach ($dangerousFiles as $file) {
        $response = $this->post(route('dokumen.upload'), [
            'kd_siswa' => $siswa->kd_siswa,
            'kd_jenis' => 1,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors(['file']);
    }
});

it('prevents duplicate unique identity records for students', function () {
    $existing = Siswa::first();

    // Try creating student with existing NIS
    $response = $this->post(route('siswa.store'), [
        'nis' => $existing->nis,
        'nisn' => '9999999999',
        'nik' => '9999999999999999',
        'nm_siswa' => 'Duplikat Siswa',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-01-01',
        'jenis_kelamin' => 'Laki-laki',
        'kd_agama' => 1,
        'alamat' => 'Alamat Uji',
        'no_kk' => '3307019999990001',
        'anak_ke' => 1,
        'jumlah_saudara' => 1,
        'orang_tua' => [
            [
                'hubungan' => 'ayah',
                'nm_ortu' => 'Ayah Uji',
            ],
        ],
    ]);

    $response->assertSessionHasErrors(['nis']);
});

it('enforces cascading data removal integrity when student is deleted', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();
    $kdSiswa = $siswa->kd_siswa;

    // Delete student
    $response = $this->delete(route('siswa.destroy', $kdSiswa));
    $response->assertRedirect(route('siswa.index'));

    // Verify student and child relations are cleaned up
    $this->assertDatabaseMissing('siswa', ['kd_siswa' => $kdSiswa]);
    $this->assertDatabaseMissing('keluarga', ['kd_siswa' => $kdSiswa]);
    $this->assertDatabaseMissing('orang_tua_wali', ['kd_siswa' => $kdSiswa]);
    $this->assertDatabaseMissing('kontak_siswa', ['kd_siswa' => $kdSiswa]);
});

it('prevents graduation of non-existent or invalid students', function () {
    $response = $this->post(route('kelulusan.store'), [
        'kd_siswa' => 999999,
        'kd_tahun' => 1,
        'tgl_lulus' => '2026-06-15',
    ]);

    $response->assertSessionHasErrors(['kd_siswa']);
});
