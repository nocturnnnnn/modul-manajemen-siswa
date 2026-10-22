<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_daftar
 * @property int $kd_siswa
 * @property string $no_pendaftaran
 * @property Carbon $tgl_daftar
 * @property string $jalur
 * @property string $asal_sekolah
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 */
class Pendaftaran extends Model
{
    protected $table = 'pendaftaran';

    protected $primaryKey = 'kd_daftar';

    protected $fillable = [
        'kd_siswa',
        'no_pendaftaran',
        'tgl_daftar',
        'jalur',
        'asal_sekolah',
    ];

    protected $casts = [
        'tgl_daftar' => 'date',
    ];

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
