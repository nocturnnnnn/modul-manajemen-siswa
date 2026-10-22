<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreAgamaRequest;
use App\Http\Requests\Master\UpdateAgamaRequest;
use App\Models\Agama;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AgamaController extends Controller
{
    /**
     * Display a listing of religion reference data.
     */
    public function index(): Response
    {
        $agamaList = Agama::withCount('siswa')
            ->orderBy('kd_agama')
            ->get();

        return Inertia::render('Master/Agama', [
            'agamaList' => $agamaList,
        ]);
    }

    /**
     * Store a newly created religion.
     */
    public function store(StoreAgamaRequest $request): RedirectResponse
    {
        Agama::create($request->validated());

        return redirect()->back()->with('success', 'Data agama baru berhasil ditambahkan.');
    }

    /**
     * Update the specified religion.
     */
    public function update(UpdateAgamaRequest $request, Agama $agama): RedirectResponse
    {
        $agama->update($request->validated());

        return redirect()->back()->with('success', 'Data agama berhasil diperbarui.');
    }

    /**
     * Remove the specified religion from database.
     */
    public function destroy(Agama $agama): RedirectResponse
    {
        if ($agama->siswa()->exists()) {
            return redirect()->back()->with(
                'error',
                'Agama tidak dapat dihapus karena masih digunakan oleh data siswa.'
            );
        }

        $agama->delete();

        return redirect()->back()->with('success', 'Data agama berhasil dihapus.');
    }
}
