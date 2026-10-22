<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mutasi extends Model
{
    use HasFactory;

    protected $table = 'mutasi';

    protected $primaryKey = 'kd_mutasi';

    protected $fillable = [
        'kd_siswa',
        'jenis_mutasi',
        'tgl_mutasi',
        'no_surat',
    ];

    protected $casts = [
        'tgl_mutasi' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
