<?php

use Illuminate\Support\Facades\Schema;

it('asserts all 15 erd tables exist in the database', function () {
    $expectedTables = [
        'agama',
        'jenis_dokumen',
        'tahun_ajaran',
        'kelas',
        'siswa',
        'keluarga',
        'orang_tua_wali',
        'kontak_siswa',
        'dokumen_siswa',
        'pendaftaran',
        'kenaikan_kelas',
        'mutasi',
        'pindah_sekolah',
        'kelulusan',
        'alumni',
    ];

    foreach ($expectedTables as $table) {
        expect(Schema::hasTable($table))->toBeTrue("Tabel {$table} harus ada di database.");
    }
});

it('asserts siswa table has all required columns', function () {
    $expectedColumns = [
        'kd_siswa',
        'nis',
        'nisn',
        'nik',
        'nm_siswa',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'kd_agama',
        'alamat',
        'foto',
        'status_siswa',
        'created_at',
        'updated_at',
    ];

    foreach ($expectedColumns as $column) {
        expect(Schema::hasColumn('siswa', $column))->toBeTrue("Kolom {$column} harus ada di tabel siswa.");
    }
});

it('asserts dokumen_siswa table has verification status and foreign keys', function () {
    expect(Schema::hasColumns('dokumen_siswa', [
        'kd_dokumen',
        'kd_siswa',
        'kd_jenis',
        'file_path',
        'status_verifikasi',
        'catatan_verifikasi',
        'tgl_upload',
    ]))->toBeTrue();
});

it('asserts kenaikan_kelas table has origin and target class columns', function () {
    expect(Schema::hasColumns('kenaikan_kelas', [
        'kd_kenaikan',
        'kd_siswa',
        'kd_tahun',
        'kd_kelas_asal',
        'kd_kelas_tujuan',
        'status',
    ]))->toBeTrue();
});
