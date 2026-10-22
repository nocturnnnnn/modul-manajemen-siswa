<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluarga';

    protected $primaryKey = 'kd_keluarga';

    protected $fillable = [
        'kd_siswa',
        'no_kk',
        'anak_ke',
        'jumlah_saudara',
    ];

    protected $casts = [
        'anak_ke' => 'integer',
        'jumlah_saudara' => 'integer',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }
}
