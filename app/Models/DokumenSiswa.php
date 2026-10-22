<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $kd_dokumen
 * @property int $kd_siswa
 * @property int $kd_jenis
 * @property string|null $nomor_dokumen
 * @property string $file_path
 * @property string $status_verifikasi
 * @property string|null $catatan_verifikasi
 * @property Carbon $tgl_upload
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Siswa|null $siswa
 * @property-read JenisDokumen|null $jenis
 */
class DokumenSiswa extends Model
{
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

    /**
     * @return BelongsTo<Siswa, $this>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'kd_siswa', 'kd_siswa');
    }

    /**
     * @return BelongsTo<JenisDokumen, $this>
     */
    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisDokumen::class, 'kd_jenis', 'kd_jenis');
    }
}
