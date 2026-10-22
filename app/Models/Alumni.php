<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_alumni
 * @property int $kd_lulus
 * @property string $kegiatan_lanjutan
 * @property string $instansi
 * @property string|null $kontak
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Kelulusan|null $kelulusan
 */
class Alumni extends Model
{
    protected $table = 'alumni';

    protected $primaryKey = 'kd_alumni';

    protected $fillable = [
        'kd_lulus',
        'kegiatan_lanjutan',
        'instansi',
        'kontak',
    ];

    /**
     * @return BelongsTo<Kelulusan, $this>
     */
    public function kelulusan(): BelongsTo
    {
        return $this->belongsTo(Kelulusan::class, 'kd_lulus', 'kd_lulus');
    }
}
