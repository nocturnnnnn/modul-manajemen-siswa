<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PindahSekolah extends Model
{
    use HasFactory;

    protected $table = 'pindah_sekolah';

    protected $primaryKey = 'kd_pindah';

    protected $fillable = [
        'kd_siswa',
        'sekolah_tujuan',
        'tgl_pindah',
        'alasan',
    ];

    protected $casts = [
        'tgl_pindah' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
