<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrangTuaWali extends Model
{
    use HasFactory;

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

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
