<?php

namespace App\Actions\Siswa;

use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteSiswaAction
{
    /**
     * Delete student and cleans up uploaded avatar photo.
     */
    public function execute(Siswa $siswa): bool
    {
        return DB::transaction(function () use ($siswa) {
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }

            return (bool) $siswa->delete();
        });
    }
}
