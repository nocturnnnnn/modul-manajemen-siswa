<?php

namespace App\Http\Controllers;

use App\Actions\Kelulusan\ProsesKelulusanAction;
use App\Http\Requests\Kelulusan\ProsesKelulusanRequest;
use App\Models\Alumni;
use App\Models\Kelulusan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KelulusanController extends Controller
{
    /**
     * Display grade 9 graduation registry and candidates.
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $kdTahun = $request->query('kd_tahun');

        $tahunAjaranList = TahunAjaran::orderByDesc('kd_tahun')->get();
        $tahunAktif = TahunAjaran::where('aktif', true)->first() ?: $tahunAjaranList->first();

        // Active grade 9 students not yet graduated
        $calonLulusan = Siswa::where('status_siswa', 'aktif')
            ->whereDoesntHave('kelulusan')
            ->whereHas('kenaikanKelas', function ($q) {
                $q->whereHas('kelasTujuan', fn ($kq) => $kq->where('tingkat', 9))
                    ->whereRaw('kd_kenaikan = (SELECT MAX(k2.kd_kenaikan) FROM kenaikan_kelas k2 WHERE k2.kd_siswa = siswa.kd_siswa)');
            })
            ->orderBy('nm_siswa')
            ->get();

        $kelulusanList = Kelulusan::with(['siswa', 'tahunAjaran', 'alumni'])
            ->when($search, function ($q, $search) {
                $q->where('no_ijazah', 'like', "%{$search}%")
                    ->orWhereHas('siswa', fn ($sq) => $sq->where('nm_siswa', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"));
            })
            ->when($kdTahun, fn ($q, $kdTahun) => $q->where('kd_tahun', $kdTahun))
            ->latest('kd_lulus')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'totalLulus' => Kelulusan::count(),
            'totalAlumni' => Alumni::count(),
            'calonTingkat9' => $calonLulusan->count(),
        ];

        return Inertia::render('Kelulusan/Index', [
            'calonLulusan' => $calonLulusan,
            'kelulusanList' => $kelulusanList,
            'tahunAjaranList' => $tahunAjaranList,
            'tahunAktif' => $tahunAktif,
            'stats' => $stats,
            'filters' => [
                'search' => $search ?? '',
                'kd_tahun' => $kdTahun ? (int) $kdTahun : null,
            ],
        ]);
    }

    /**
     * Process official graduation for a grade 9 student.
     */
    public function store(ProsesKelulusanRequest $request, ProsesKelulusanAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Kelulusan siswa berhasil disahkan.');
    }

    /**
     * Cancel or delete a graduation record.
     */
    public function destroy(Kelulusan $kelulusan): RedirectResponse
    {
        $siswa = $kelulusan->siswa;
        $kelulusan->delete();

        if ($siswa && in_array($siswa->status_siswa, ['lulus', 'alumni'])) {
            $siswa->update(['status_siswa' => 'aktif']);
        }

        return back()->with('success', 'Catatan kelulusan berhasil dibatalkan.');
    }
}
