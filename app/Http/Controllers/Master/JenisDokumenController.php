<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\StoreJenisDokumenRequest;
use App\Http\Requests\Master\UpdateJenisDokumenRequest;
use App\Models\JenisDokumen;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class JenisDokumenController extends Controller
{
    /**
     * Display a listing of document type references.
     */
    public function index(): Response
    {
        $jenisDokumenList = JenisDokumen::withCount('dokumenSiswa')
            ->orderBy('kd_jenis')
            ->get();

        return Inertia::render('Master/JenisDokumen', [
            'jenisDokumenList' => $jenisDokumenList,
        ]);
    }

    /**
     * Store a newly created document type.
     */
    public function store(StoreJenisDokumenRequest $request): RedirectResponse
    {
        JenisDokumen::create($request->validated());

        return redirect()->back()->with('success', 'Jenis dokumen baru berhasil ditambahkan.');
    }

    /**
     * Update the specified document type.
     */
    public function update(UpdateJenisDokumenRequest $request, JenisDokumen $jenisDokumen): RedirectResponse
    {
        $jenisDokumen->update($request->validated());

        return redirect()->back()->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    /**
     * Remove the specified document type from database.
     */
    public function destroy(JenisDokumen $jenisDokumen): RedirectResponse
    {
        if ($jenisDokumen->dokumenSiswa()->exists()) {
            return redirect()->back()->with(
                'error',
                'Jenis dokumen tidak dapat dihapus karena masih digunakan oleh berkas dokumen siswa.'
            );
        }

        $jenisDokumen->delete();

        return redirect()->back()->with('success', 'Jenis dokumen berhasil dihapus.');
    }
}
