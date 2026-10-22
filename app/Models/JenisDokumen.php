<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisDokumen extends Model
{
    use HasFactory;

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
     */
    public function dokumenSiswa(): HasMany
    {
        return $this->hasMany(DokumenSiswa::class, 'kd_jenis', 'kd_jenis');
    }
}
