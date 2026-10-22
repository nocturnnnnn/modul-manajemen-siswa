<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_mutasi
 * @property int $kd_siswa
 * @property string $jenis_mutasi
 * @property Carbon $tgl_mutasi
 * @property string $no_surat
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 */
class Mutasi extends Model
{
    protected $table = 'mutasi';

    protected $primaryKey = 'kd_mutasi';

    protected $fillable = [
        'kd_siswa',
        'jenis_mutasi',
        'tgl_mutasi',
        'no_surat',
    ];

    protected $casts = [
        'tgl_mutasi' => 'date',
    ];

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
