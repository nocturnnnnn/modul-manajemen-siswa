<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KenaikanKelas extends Model
{
    use HasFactory;

    protected $table = 'kenaikan_kelas';

    protected $primaryKey = 'kd_kenaikan';

    protected $fillable = [
        'kd_siswa',
        'kd_tahun',
        'kd_kelas_asal',
        'kd_kelas_tujuan',
        'status',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'kd_tahun', 'kd_tahun');
    }

    public function kelasAsal(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kd_kelas_asal', 'kd_kelas');
    }

    public function kelasTujuan(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kd_kelas_tujuan', 'kd_kelas');
    }
}
