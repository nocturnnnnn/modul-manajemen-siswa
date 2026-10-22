<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumni extends Model
{
    use HasFactory;

    protected $table = 'alumni';

    protected $primaryKey = 'kd_alumni';

    protected $fillable = [
        'kd_lulus',
        'kegiatan_lanjutan',
        'instansi',
        'kontak',
    ];

    public function kelulusan(): BelongsTo
    {
        return $this->belongsTo(Kelulusan::class, 'kd_lulus', 'kd_lulus');
    }
}
