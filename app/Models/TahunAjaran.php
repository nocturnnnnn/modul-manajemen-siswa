<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use HasFactory;

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
     */
    public function kenaikanKelas(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'kd_tahun', 'kd_tahun');
    }

    /**
     * Relasi ke kelulusan pada tahun ajaran ini.
     */
    public function kelulusan(): HasMany
    {
        return $this->hasMany(Kelulusan::class, 'kd_tahun', 'kd_tahun');
    }
}
