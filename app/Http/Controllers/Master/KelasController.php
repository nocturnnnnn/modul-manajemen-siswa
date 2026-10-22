<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreKelasRequest;
use App\Http\Requests\Master\UpdateKelasRequest;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KelasController extends Controller
{
    /**
     * Display a listing of classroom reference data.
     */
    public function index(Request $request): Response
    {
        $query = Kelas::query()
            ->withCount(['kenaikanKelasAsal', 'kenaikanKelasTujuan']);

        if ($search = $request->input('search')) {
            $query->where('nm_kelas', 'like', "%{$search}%");
        }

        if ($tingkat = $request->input('tingkat')) {
            $query->where('tingkat', (int) $tingkat);
        }

        $kelasList = $query
            ->orderBy('tingkat')
            ->orderBy('nm_kelas')
            ->get();

        return Inertia::render('Master/Kelas', [
            'kelasList' => $kelasList,
            'filters' => $request->only(['search', 'tingkat']),
        ]);
    }

    /**
     * Store a newly created classroom.
     */
    public function store(StoreKelasRequest $request): RedirectResponse
    {
        Kelas::create($request->validated());

        return redirect()->back()->with('success', 'Rombel kelas baru berhasil ditambahkan.');
    }

    /**
     * Update the specified classroom.
     */
    public function update(UpdateKelasRequest $request, Kelas $kelas): RedirectResponse
    {
        $kelas->update($request->validated());

        return redirect()->back()->with('success', 'Rombel kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified classroom from database.
     */
    public function destroy(Kelas $kelas): RedirectResponse
    {
        if ($kelas->kenaikanKelasAsal()->exists() || $kelas->kenaikanKelasTujuan()->exists()) {
            return redirect()->back()->with(
                'error',
                'Kelas tidak dapat dihapus karena masih tercatat pada riwayat akademik siswa.'
            );
        }

        $kelas->delete();

        return redirect()->back()->with('success', 'Rombel kelas berhasil dihapus.');
    }
}
