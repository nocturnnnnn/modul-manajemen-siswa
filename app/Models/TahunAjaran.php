<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_tahun
 * @property string $nm_tahun
 * @property string $semester
 * @property bool $aktif
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, KenaikanKelas> $kenaikanKelas
 * @property-read Collection<int, Kelulusan> $kelulusan
 */
class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $primaryKey = 'kd_tahun';

    protected $fillable = [
        'nm_tahun',
        'semester',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    /**
     * Relasi ke riwayat kenaikan kelas pada tahun ajaran ini.
     *
     * @return HasMany<KenaikanKelas, $this>
     */
    public function kenaikanKelas(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'kd_tahun', 'kd_tahun');
    }

    /**
     * Relasi ke kelulusan pada tahun ajaran ini.
     *
     * @return HasMany<Kelulusan, $this>
     */
    public function kelulusan(): HasMany
    {
        return $this->hasMany(Kelulusan::class, 'kd_tahun', 'kd_tahun');
    }
}
