<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontakSiswa extends Model
{
    use HasFactory;

    protected $table = 'kontak_siswa';

    protected $primaryKey = 'kd_kontak';

    protected $fillable = [
        'kd_siswa',
        'jenis_kontak',
        'nomor',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
