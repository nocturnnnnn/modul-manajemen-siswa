<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_kenaikan
 * @property int $kd_siswa
 * @property int $kd_tahun
 * @property int $kd_kelas_asal
 * @property int $kd_kelas_tujuan
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 * @property-read TahunAjaran|null $tahunAjaran
 * @property-read Kelas|null $kelasAsal
 * @property-read Kelas|null $kelasTujuan
 */
class KenaikanKelas extends Model
{
    protected $table = 'kenaikan_kelas';

    protected $primaryKey = 'kd_kenaikan';

    protected $fillable = [
        'kd_siswa',
        'kd_tahun',
        'kd_kelas_asal',
        'kd_kelas_tujuan',
        'status',
    ];

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return BelongsTo<TahunAjaran, $this>
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'kd_tahun', 'kd_tahun');
    }

    /**
     * @return BelongsTo<Kelas, $this>
     */
    public function kelasAsal(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kd_kelas_asal', 'kd_kelas');
    }

    /**
     * @return BelongsTo<Kelas, $this>
     */
    public function kelasTujuan(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kd_kelas_tujuan', 'kd_kelas');
    }
}
