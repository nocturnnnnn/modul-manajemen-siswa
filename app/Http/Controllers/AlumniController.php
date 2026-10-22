<?php

namespace App\Http\Controllers;

use App\Actions\Kelulusan\UpdateAlumniAction;
use App\Http\Requests\Kelulusan\SimpanAlumniRequest;
use App\Models\Alumni;
use App\Models\Kelulusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AlumniController extends Controller
{
    /**
     * Display the alumni directory and tracking portal.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();
        $kegiatan = $request->string('kegiatan')->toString();

        $alumniList = Alumni::with(['kelulusan.siswa', 'kelulusan.tahunAjaran'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('instansi', 'like', "%{$search}%")
                    ->orWhere('kegiatan_lanjutan', 'like', "%{$search}%")
                    ->orWhereHas('kelulusan.siswa', function ($sq) use ($search) {
                        $sq->where('nm_siswa', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            })
            ->when($kegiatan !== '', fn ($q) => $q->where('kegiatan_lanjutan', $kegiatan))
            ->latest('kd_alumni')
            ->paginate(10)
            ->withQueryString();

        $lulusanBelumAlumni = Kelulusan::doesntHave('alumni')
            ->with(['siswa', 'tahunAjaran'])
            ->latest('kd_lulus')
            ->get();

        $stats = [
            'totalAlumni' => Alumni::count(),
            'melanjutkanSMA' => Alumni::where('kegiatan_lanjutan', 'like', '%SMA%')->count(),
            'melanjutkanSMK' => Alumni::where('kegiatan_lanjutan', 'like', '%SMK%')->count(),
            'belumTercatat' => $lulusanBelumAlumni->count(),
        ];

        return Inertia::render('Kelulusan/Alumni', [
            'alumniList' => $alumniList,
            'lulusanBelumAlumni' => $lulusanBelumAlumni,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'kegiatan' => $kegiatan,
            ],
        ]);
    }

    /**
     * Store or update alumni directory record.
     */
    public function store(SimpanAlumniRequest $request, UpdateAlumniAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Data direktori alumni berhasil disimpan.');
    }

    /**
     * Remove an alumni record and revert status back to 'lulus'.
     */
    public function destroy(Alumni $alumni): RedirectResponse
    {
        $kelulusan = $alumni->kelulusan;
        $alumni->delete();

        if ($kelulusan && $kelulusan->siswa) {
            $kelulusan->siswa->update(['status_siswa' => 'lulus']);
        }

        return back()->with('success', 'Data alumni berhasil dihapus.');
    }
}
