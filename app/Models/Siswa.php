<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $primaryKey = 'kd_siswa';

    protected $fillable = [
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
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function agama(): BelongsTo
    {
        return $this->belongsTo(Agama::class, 'kd_agama', 'kd_agama');
    }

    public function keluarga(): HasOne
    {
        return $this->hasOne(Keluarga::class, 'kd_siswa', 'kd_siswa');
    }

    public function orangTuaWali(): HasMany
    {
        return $this->hasMany(OrangTuaWali::class, 'kd_siswa', 'kd_siswa');
    }

    public function kontak(): HasMany
    {
        return $this->hasMany(KontakSiswa::class, 'kd_siswa', 'kd_siswa');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenSiswa::class, 'kd_siswa', 'kd_siswa');
    }

    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'kd_siswa', 'kd_siswa');
    }

    public function kenaikanKelas(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'kd_siswa', 'kd_siswa');
    }

    public function mutasi(): HasMany
    {
        return $this->hasMany(Mutasi::class, 'kd_siswa', 'kd_siswa');
    }

    public function pindahSekolah(): HasMany
    {
        return $this->hasMany(PindahSekolah::class, 'kd_siswa', 'kd_siswa');
    }

    public function kelulusan(): HasOne
    {
        return $this->hasOne(Kelulusan::class, 'kd_siswa', 'kd_siswa');
    }
}
