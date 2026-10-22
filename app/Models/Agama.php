<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_agama
 * @property string $nm_agama
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Siswa> $siswa
 */
class Agama extends Model
{
    protected $table = 'agama';

    protected $primaryKey = 'kd_agama';

    protected $fillable = [
        'nm_agama',
    ];

    /**
     * Relasi ke siswa yang menganut agama ini.
     *
     * @return HasMany<Siswa, $this>
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kd_agama', 'kd_agama');
    }
}
