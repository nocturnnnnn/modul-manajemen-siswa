<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_jenis
 * @property string $kode
 * @property string $nm_jenis
 * @property bool $wajib
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, DokumenSiswa> $dokumenSiswa
 */
class JenisDokumen extends Model
{
    protected $table = 'jenis_dokumen';

    protected $primaryKey = 'kd_jenis';

    protected $fillable = [
        'kode',
        'nm_jenis',
        'wajib',
    ];

    protected $casts = [
        'wajib' => 'boolean',
    ];

    /**
     * Relasi ke dokumen siswa yang termasuk jenis ini.
     *
     * @return HasMany<DokumenSiswa, $this>
     */
    public function dokumenSiswa(): HasMany
    {
        return $this->hasMany(DokumenSiswa::class, 'kd_jenis', 'kd_jenis');
    }
}
