<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agama extends Model
{
    use HasFactory;

    protected $table = 'agama';

    protected $primaryKey = 'kd_agama';

    protected $fillable = [
        'nm_agama',
    ];

    /**
     * Relasi ke siswa yang menganut agama ini.
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kd_agama', 'kd_agama');
    }
}
