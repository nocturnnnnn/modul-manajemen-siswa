<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_pindah
 * @property int $kd_siswa
 * @property string $sekolah_tujuan
 * @property Carbon $tgl_pindah
 * @property string $alasan
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 */
class PindahSekolah extends Model
{
    protected $table = 'pindah_sekolah';

    protected $primaryKey = 'kd_pindah';

    protected $fillable = [
        'kd_siswa',
        'sekolah_tujuan',
        'tgl_pindah',
        'alasan',
    ];

    protected $casts = [
        'tgl_pindah' => 'date',
    ];

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
