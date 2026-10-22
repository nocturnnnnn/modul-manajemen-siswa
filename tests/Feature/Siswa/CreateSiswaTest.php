<?php

use App\Actions\Siswa\CreateSiswaAction;
use App\Models\Siswa;
use Database\Seeders\AgamaSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\SiswaSampleSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

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

it('can view siswa creation form', function () {
    $response = $this->get(route('siswa.create'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Siswa/Create')
        ->has('agamaList')
    );
});

it('validates uniqueness of nis, nisn, and nik', function () {
    $existing = Siswa::first();

    $response = $this->post(route('siswa.store'), [
        'nis' => $existing->nis,
        'nisn' => $existing->nisn,
        'nik' => $existing->nik,
        'nm_siswa' => 'Nama Baru Siswa',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-05-10',
        'jenis_kelamin' => 'Laki-laki',
        'kd_agama' => 1,
        'alamat' => 'Jl. Bhayangkara No. 1',
        'keluarga' => [
            'no_kk' => '3307010101010002',
            'anak_ke' => 1,
            'jumlah_saudara' => 0,
        ],
        'orang_tua' => [
            ['hubungan' => 'ayah', 'nm_ortu' => 'Ayah Baru'],
        ],
    ]);

    $response->assertSessionHasErrors(['nis', 'nisn', 'nik']);
});

it('creates siswa with keluarga, ortu, and kontak in single transaction', function () {
    Storage::fake('public');

    $foto = UploadedFile::fake()->create('avatar.jpg', 300, 'image/jpeg');

    $payload = [
        'nis' => '202607999',
        'nisn' => '0099999999',
        'nik' => '3307999999999999',
        'nm_siswa' => 'Siswa Baru Integrasi',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-08-17',
        'jenis_kelamin' => 'Perempuan',
        'kd_agama' => 1,
        'alamat' => 'Jl. Mayjend Bambang Sugeng No. 10',
        'foto' => $foto,
        'keluarga' => [
            'no_kk' => '3307888888888888',
            'anak_ke' => 2,
            'jumlah_saudara' => 1,
        ],
        'orang_tua' => [
            [
                'hubungan' => 'ayah',
                'nm_ortu' => 'Bambang Hartono',
                'nik' => '3307111111111111',
                'pekerjaan' => 'Guru',
                'pendidikan' => 'S1',
                'no_hp' => '081234567890',
            ],
            [
                'hubungan' => 'ibu',
                'nm_ortu' => 'Siti Aminah',
                'nik' => '3307222222222222',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'pendidikan' => 'SMA',
                'no_hp' => '081234567891',
            ],
        ],
        'kontak' => [
            ['jenis_kontak' => 'WhatsApp', 'nomor' => '081234567890'],
        ],
        'pendaftaran' => [
            'no_pendaftaran' => 'REG-2026-999',
            'tgl_daftar' => '2026-06-15',
            'jalur' => 'Prestasi',
            'asal_sekolah' => 'SD Kristen Wonosobo',
        ],
    ];

    $response = $this->post(route('siswa.store'), $payload);

    $response->assertRedirect();
    $this->assertDatabaseHas('siswa', [
        'nis' => '202607999',
        'nm_siswa' => 'Siswa Baru Integrasi',
    ]);

    $siswa = Siswa::where('nis', '202607999')->first();
    expect($siswa)->not->toBeNull();
    expect($siswa->foto)->not->toBeNull();
    Storage::disk('public')->assertExists($siswa->foto);

    $this->assertDatabaseHas('keluarga', [
        'kd_siswa' => $siswa->kd_siswa,
        'no_kk' => '3307888888888888',
    ]);

    $this->assertDatabaseHas('orang_tua_wali', [
        'kd_siswa' => $siswa->kd_siswa,
        'nm_ortu' => 'Bambang Hartono',
    ]);

    $this->assertDatabaseHas('kontak_siswa', [
        'kd_siswa' => $siswa->kd_siswa,
        'nomor' => '081234567890',
    ]);

    $this->assertDatabaseHas('pendaftaran', [
        'kd_siswa' => $siswa->kd_siswa,
        'no_pendaftaran' => 'REG-2026-999',
    ]);
});

it('rejects photo upload larger than 2mb or non-image format', function () {
    Storage::fake('public');

    // File terlalu besar (> 2048 KB)
    $largePhoto = UploadedFile::fake()->create('large.jpg', 3000, 'image/jpeg');

    $response = $this->post(route('siswa.store'), [
        'nis' => '202607888',
        'nisn' => '0088888888',
        'nik' => '3307888888888887',
        'nm_siswa' => 'Siswa Foto Besar',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-08-17',
        'jenis_kelamin' => 'Laki-laki',
        'kd_agama' => 1,
        'alamat' => 'Alamat',
        'foto' => $largePhoto,
        'keluarga' => [
            'no_kk' => '3307888888888888',
            'anak_ke' => 1,
            'jumlah_saudara' => 0,
        ],
        'orang_tua' => [
            ['hubungan' => 'ayah', 'nm_ortu' => 'Ayah'],
        ],
    ]);

    $response->assertSessionHasErrors(['foto']);
});

it('rolls back database if creation encounters error', function () {
    $action = new CreateSiswaAction;

    $invalidPayload = [
        'nis' => '202607777',
        'nisn' => '0077777777',
        'nik' => '3307777777777777',
        'nm_siswa' => 'Test Rollback',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-01-01',
        'jenis_kelamin' => 'Laki-laki',
        'kd_agama' => 1,
        'alamat' => 'Alamat Rollback',
        'keluarga' => [
            'no_kk' => '3307777777777777',
            'anak_ke' => 1,
            'jumlah_saudara' => 0,
        ],
        'orang_tua' => [
            [
                'hubungan' => 'ayah',
                'nm_ortu' => null, // Will cause exception because nm_ortu is NOT NULL
            ],
        ],
    ];

    try {
        $action->execute($invalidPayload);
        $this->fail('Harusnya melempar exception karena nm_ortu bernilai null');
    } catch (Throwable) {
        // Expected rollback
    }

    // Assert student was rolled back and NOT inserted into database
    $this->assertDatabaseMissing('siswa', [
        'nis' => '202607777',
    ]);
});
