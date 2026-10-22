<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreTahunAjaranRequest;
use App\Http\Requests\Master\UpdateTahunAjaranRequest;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TahunAjaranController extends Controller
{
    /**
     * Display a listing of academic years.
     */
    public function index(): Response
    {
        $tahunAjaranList = TahunAjaran::withCount(['kenaikanKelas', 'kelulusan'])
            ->orderByDesc('nm_tahun')
            ->orderBy('semester')
            ->get();

        return Inertia::render('Master/TahunAjaran', [
            'tahunAjaranList' => $tahunAjaranList,
        ]);
    }

    /**
     * Store a newly created academic year.
     */
    public function store(StoreTahunAjaranRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['aktif'])) {
            TahunAjaran::where('aktif', true)->update(['aktif' => false]);
        }

        TahunAjaran::create($data);

        return redirect()->back()->with('success', 'Tahun ajaran baru berhasil ditambahkan.');
    }

    /**
     * Update the specified academic year.
     */
    public function update(UpdateTahunAjaranRequest $request, TahunAjaran $tahunAjaran): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['aktif'])) {
            TahunAjaran::where('kd_tahun', '!=', $tahunAjaran->kd_tahun)->update(['aktif' => false]);
        }

        $tahunAjaran->update($data);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Set the specified academic year as active.
     */
    public function activate(TahunAjaran $tahunAjaran): RedirectResponse
    {
        TahunAjaran::where('aktif', true)->update(['aktif' => false]);
        $tahunAjaran->update(['aktif' => true]);

        return redirect()->back()->with(
            'success',
            "Tahun ajaran {$tahunAjaran->nm_tahun} ({$tahunAjaran->semester}) berhasil diaktifkan sebagai tahun ajaran berjalan."
        );
    }

    /**
     * Remove the specified academic year from database.
     */
    public function destroy(TahunAjaran $tahunAjaran): RedirectResponse
    {
        if ($tahunAjaran->aktif) {
            return redirect()->back()->with(
                'error',
                'Tahun ajaran yang sedang aktif tidak dapat dihapus.'
            );
        }

        if ($tahunAjaran->kenaikanKelas()->exists() || $tahunAjaran->kelulusan()->exists()) {
            return redirect()->back()->with(
                'error',
                'Tahun ajaran tidak dapat dihapus karena memiliki riwayat akademik/kelulusan.'
            );
        }

        $tahunAjaran->delete();

        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
