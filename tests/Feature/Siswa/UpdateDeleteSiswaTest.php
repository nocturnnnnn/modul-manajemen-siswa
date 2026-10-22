<?php

use App\Models\Siswa;
use Database\Seeders\AgamaSeeder;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\KelasSeeder;
use Database\Seeders\SiswaSampleSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

it('can view siswa detail profile', function () {
    $siswa = Siswa::first();

    $response = $this->get(route('siswa.show', $siswa->kd_siswa));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Siswa/Show')
        ->where('siswa.kd_siswa', $siswa->kd_siswa)
        ->has('siswa.keluarga')
        ->has('siswa.orang_tua_wali')
    );
});

it('can view siswa edit form', function () {
    $siswa = Siswa::first();

    $response = $this->get(route('siswa.edit', $siswa->kd_siswa));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Siswa/Edit')
        ->where('siswa.kd_siswa', $siswa->kd_siswa)
        ->has('agamaList')
    );
});

it('updates siswa data and relationships successfully', function () {
    $siswa = Siswa::where('nis', '202607001')->first();

    $payload = [
        'nis' => $siswa->nis,
        'nisn' => $siswa->nisn,
        'nik' => $siswa->nik,
        'nm_siswa' => 'Nama Siswa Telah Diubah',
        'tempat_lahir' => 'Wonosobo',
        'tgl_lahir' => '2012-05-10',
        'jenis_kelamin' => 'Laki-laki',
        'kd_agama' => 1,
        'alamat' => 'Alamat Baru No. 99',
        'status_siswa' => 'aktif',
        'keluarga' => [
            'no_kk' => '3307010101019999',
            'anak_ke' => 1,
            'jumlah_saudara' => 2,
        ],
        'orang_tua' => [
            [
                'hubungan' => 'ayah',
                'nm_ortu' => 'Ayah Terupdate',
                'nik' => '3307111111119999',
                'pekerjaan' => 'Wiraswasta',
                'pendidikan' => 'S1',
                'no_hp' => '081299999999',
            ],
        ],
    ];

    $response = $this->put(route('siswa.update', $siswa->kd_siswa), $payload);

    $response->assertRedirect(route('siswa.show', $siswa->kd_siswa));
    $this->assertDatabaseHas('siswa', [
        'kd_siswa' => $siswa->kd_siswa,
        'nm_siswa' => 'Nama Siswa Telah Diubah',
        'alamat' => 'Alamat Baru No. 99',
    ]);

    $this->assertDatabaseHas('keluarga', [
        'kd_siswa' => $siswa->kd_siswa,
        'no_kk' => '3307010101019999',
    ]);
});

it('deletes siswa successfully and cascades related records', function () {
    $siswa = Siswa::where('status_siswa', 'aktif')->first();
    $kdSiswa = $siswa->kd_siswa;

    $response = $this->delete(route('siswa.destroy', $kdSiswa));

    $response->assertRedirect(route('siswa.index'));
    $this->assertDatabaseMissing('siswa', [
        'kd_siswa' => $kdSiswa,
    ]);

    // Check cascade delete on keluarga and orang_tua_wali
    $this->assertDatabaseMissing('keluarga', [
        'kd_siswa' => $kdSiswa,
    ]);
    $this->assertDatabaseMissing('orang_tua_wali', [
        'kd_siswa' => $kdSiswa,
    ]);
});
