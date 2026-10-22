<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';

    protected $primaryKey = 'kd_daftar';

    protected $fillable = [
        'kd_siswa',
        'no_pendaftaran',
        'tgl_daftar',
        'jalur',
        'asal_sekolah',
    ];

    protected $casts = [
        'tgl_daftar' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
