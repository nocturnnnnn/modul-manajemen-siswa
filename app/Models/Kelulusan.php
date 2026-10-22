<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_lulus
 * @property int $kd_siswa
 * @property int $kd_tahun
 * @property Carbon $tgl_lulus
 * @property string|null $no_ijazah
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 * @property-read TahunAjaran|null $tahunAjaran
 * @property-read Alumni|null $alumni
 */
class Kelulusan extends Model
{
    protected $table = 'kelulusan';

    protected $primaryKey = 'kd_lulus';

    protected $fillable = [
        'kd_siswa',
        'kd_tahun',
        'tgl_lulus',
        'no_ijazah',
    ];

    protected $casts = [
        'tgl_lulus' => 'date',
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
     * @return HasOne<Alumni, $this>
     */
    public function alumni(): HasOne
    {
        return $this->hasOne(Alumni::class, 'kd_lulus', 'kd_lulus');
    }
}
