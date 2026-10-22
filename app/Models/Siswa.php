<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_siswa
 * @property string $nis
 * @property string $nisn
 * @property string $nik
 * @property string $nm_siswa
 * @property string $tempat_lahir
 * @property Carbon $tgl_lahir
 * @property string $jenis_kelamin
 * @property int $kd_agama
 * @property string $alamat
 * @property string|null $foto
 * @property string $status_siswa
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Agama|null $agama
 * @property-read Keluarga|null $keluarga
 * @property-read Collection<int, OrangTuaWali> $orangTuaWali
 * @property-read Collection<int, KontakSiswa> $kontak
 * @property-read Collection<int, DokumenSiswa> $dokumen
 * @property-read Collection<int, Pendaftaran> $pendaftaran
 * @property-read Collection<int, KenaikanKelas> $kenaikanKelas
 * @property-read Collection<int, Mutasi> $mutasi
 * @property-read Collection<int, PindahSekolah> $pindahSekolah
 * @property-read Kelulusan|null $kelulusan
 */
class Siswa extends Model
{
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

    /**
     * @return BelongsTo<Agama, $this>
     */
    public function agama(): BelongsTo
    {
        return $this->belongsTo(Agama::class, 'kd_agama', 'kd_agama');
    }

    /**
     * @return HasOne<Keluarga, $this>
     */
    public function keluarga(): HasOne
    {
        return $this->hasOne(Keluarga::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return HasMany<OrangTuaWali, $this>
     */
    public function orangTuaWali(): HasMany
    {
        return $this->hasMany(OrangTuaWali::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return HasMany<KontakSiswa, $this>
     */
    public function kontak(): HasMany
    {
        return $this->hasMany(KontakSiswa::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return HasMany<DokumenSiswa, $this>
     */
    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenSiswa::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return HasMany<Pendaftaran, $this>
     */
    public function pendaftaran(): HasMany
    {
        return $this->hasMany(Pendaftaran::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return HasMany<KenaikanKelas, $this>
     */
    public function kenaikanKelas(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return HasMany<Mutasi, $this>
     */
    public function mutasi(): HasMany
    {
        return $this->hasMany(Mutasi::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return HasMany<PindahSekolah, $this>
     */
    public function pindahSekolah(): HasMany
    {
        return $this->hasMany(PindahSekolah::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return HasOne<Kelulusan, $this>
     */
    public function kelulusan(): HasOne
    {
        return $this->hasOne(Kelulusan::class, 'kd_siswa', 'kd_siswa');
    }
}
