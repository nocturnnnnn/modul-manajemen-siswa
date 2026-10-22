<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_keluarga
 * @property int $kd_siswa
 * @property string $no_kk
 * @property int $anak_ke
 * @property int $jumlah_saudara
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 */
class Keluarga extends Model
{
    protected $table = 'keluarga';

    protected $primaryKey = 'kd_keluarga';

    protected $fillable = [
        'kd_siswa',
        'no_kk',
        'anak_ke',
        'jumlah_saudara',
    ];

    protected $casts = [
        'anak_ke' => 'integer',
        'jumlah_saudara' => 'integer',
    ];

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
