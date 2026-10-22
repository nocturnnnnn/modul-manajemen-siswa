<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_kontak
 * @property int $kd_siswa
 * @property string $jenis_kontak
 * @property string $nomor
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 */
class KontakSiswa extends Model
{
    protected $table = 'kontak_siswa';

    protected $primaryKey = 'kd_kontak';

    protected $fillable = [
        'kd_siswa',
        'jenis_kontak',
        'nomor',
    ];

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
