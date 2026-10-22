<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenSiswa extends Model
{
    use HasFactory;

    protected $table = 'dokumen_siswa';

    protected $primaryKey = 'kd_dokumen';

    protected $fillable = [
        'kd_siswa',
        'kd_jenis',
        'nomor_dokumen',
        'file_path',
        'status_verifikasi',
        'catatan_verifikasi',
        'tgl_upload',
    ];

    protected $casts = [
        'tgl_upload' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisDokumen::class, 'kd_jenis', 'kd_jenis');
    }
}
