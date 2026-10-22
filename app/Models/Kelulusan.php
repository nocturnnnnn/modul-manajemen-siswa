<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kelulusan extends Model
{
    use HasFactory;

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

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'kd_tahun', 'kd_tahun');
    }

    public function alumni(): HasOne
    {
        return $this->hasOne(Alumni::class, 'kd_lulus', 'kd_lulus');
    }
}
