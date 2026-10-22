<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

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
     */
    public function kenaikanKelasAsal(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'kd_kelas_asal', 'kd_kelas');
    }

    /**
     * Relasi ke riwayat kenaikan kelas sebagai kelas tujuan.
     */
    public function kenaikanKelasTujuan(): HasMany
    {
        return $this->hasMany(KenaikanKelas::class, 'kd_kelas_tujuan', 'kd_kelas');
    }
}
