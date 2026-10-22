<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_kelas
 * @property string $nm_kelas
 * @property int $tingkat
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, KenaikanKelas> $kenaikanKelasAsal
 * @property-read Collection<int, KenaikanKelas> $kenaikanKelasTujuan
 */
class Kelas extends Model
{
    protected $table = 'kelas';

    protected $primaryKey = 'kd_kelas';

    protected $fillable = [
        'nm_kelas',
        'tingkat',
    ];

    protected $casts = [
        'tingkat' => 'integer',
    ];

    /**
     * Relasi ke riwayat kenaikan kelas sebagai kelas asal.
     *
     * @return HasMany<KenaikanKelas, $this>
     */
    public function kenaikanKelasAsal(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'kd_kelas_asal', 'kd_kelas');
    }

    /**
     * Relasi ke riwayat kenaikan kelas sebagai kelas tujuan.
     *
     * @return HasMany<KenaikanKelas, $this>
     */
    public function kenaikanKelasTujuan(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'kd_kelas_tujuan', 'kd_kelas');
    }
}
