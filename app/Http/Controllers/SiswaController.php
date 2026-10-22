<?php

namespace App\Http\Controllers;

use App\Actions\Siswa\CreateSiswaAction;
use App\Actions\Siswa\DeleteSiswaAction;
use App\Actions\Siswa\UpdateSiswaAction;
use App\Http\Requests\Siswa\StoreSiswaRequest;
use App\Http\Requests\Siswa\UpdateSiswaRequest;
use App\Models\Agama;
use App\Models\JenisDokumen;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiswaController extends Controller
{
    /**
     * Display a listing of students with search and filter parameters.
     */
    public function index(Request $request): Response
    {
        $query = Siswa::query()->with(['agama', 'keluarga']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nm_siswa', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status_siswa', $status);
        }

        if ($agama = $request->input('agama')) {
            $query->where('kd_agama', (int) $agama);
        }

        $siswaList = $query
            ->orderBy('nm_siswa')
            ->paginate(10)
            ->withQueryString();

        $agamaList = Agama::orderBy('nm_agama')->get();

        return Inertia::render('Siswa/Index', [
            'siswaList' => $siswaList,
            'agamaList' => $agamaList,
            'filters' => $request->only(['search', 'status', 'agama']),
        ]);
    }

    /**
     * Show the form for creating a new student.
     */
    public function create(): Response
    {
        $agamaList = Agama::orderBy('nm_agama')->get();

        return Inertia::render('Siswa/Create', [
            'agamaList' => $agamaList,
        ]);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(StoreSiswaRequest $request, CreateSiswaAction $action): RedirectResponse
    {
        $siswa = $action->execute(
            $request->validated(),
            $request->file('foto')
        );

        return redirect()
            ->route('siswa.show', $siswa->kd_siswa)
            ->with('success', "Data siswa {$siswa->nm_siswa} berhasil disimpan.");
    }

    /**
     * Display the specified student's profile, family, contacts, and history.
     */
    public function show(Siswa $siswa): Response
    {
        $siswa->load([
            'keluarga',
            'orangTuaWali',
            'kontak',
            'pendaftaran',
            'agama',
            'dokumen.jenis',
            'kenaikanKelas.tahunAjaran',
            'kenaikanKelas.kelasAsal',
            'kenaikanKelas.kelasTujuan',
            'mutasi',
            'pindahSekolah',
            'kelulusan.alumni',
            'kelulusan.tahunAjaran',
        ]);

        return Inertia::render('Siswa/Show', [
            'siswa' => $siswa,
            'jenisDokumenList' => JenisDokumen::orderBy('nm_jenis')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Siswa $siswa): Response
    {
        $siswa->load(['keluarga', 'orangTuaWali', 'kontak', 'pendaftaran', 'agama']);
        $agamaList = Agama::orderBy('nm_agama')->get();

        return Inertia::render('Siswa/Edit', [
            'siswa' => $siswa,
            'agamaList' => $agamaList,
        ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa, UpdateSiswaAction $action): RedirectResponse
    {
        $updatedSiswa = $action->execute(
            $siswa,
            $request->validated(),
            $request->file('foto')
        );

        return redirect()
            ->route('siswa.show', $updatedSiswa->kd_siswa)
            ->with('success', "Data profil siswa {$updatedSiswa->nm_siswa} berhasil diperbarui.");
    }

    /**
     * Remove the specified student from database.
     */
    public function destroy(Siswa $siswa, DeleteSiswaAction $action): RedirectResponse
    {
        $nmSiswa = $siswa->nm_siswa;
        $action->execute($siswa);

        return redirect()
            ->route('siswa.index')
            ->with('success', "Data siswa {$nmSiswa} telah dihapus dari sistem.");
    }
}
