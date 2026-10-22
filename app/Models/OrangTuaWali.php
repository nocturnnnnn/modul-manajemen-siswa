<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_ortu
 * @property int $kd_siswa
 * @property string $hubungan
 * @property string $nm_ortu
 * @property string|null $nik
 * @property string|null $pekerjaan
 * @property string|null $pendidikan
 * @property string|null $no_hp
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 */
class OrangTuaWali extends Model
{
    protected $table = 'orang_tua_wali';

    protected $primaryKey = 'kd_ortu';

    protected $fillable = [
        'kd_siswa',
        'hubungan',
        'nm_ortu',
        'nik',
        'pekerjaan',
        'pendidikan',
        'no_hp',
    ];

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
