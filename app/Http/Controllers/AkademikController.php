<?php

namespace App\Http\Controllers;

use App\Actions\Akademik\ProcessKenaikanKelasAction;
use App\Actions\Akademik\ProcessTinggalKelasAction;
use App\Http\Requests\Akademik\KenaikanKelasRequest;
use App\Models\Kelas;
use App\Models\KenaikanKelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AkademikController extends Controller
{
    /**
     * Display the class promotion and retention evaluation interface.
     */
    public function index(Request $request): Response
    {
        $tahunAjaranAktif = TahunAjaran::where('aktif', true)->first()
            ?: TahunAjaran::latest('kd_tahun')->first();

        $kdTahun = $request->query('kd_tahun', $tahunAjaranAktif?->kd_tahun);
        $selectedTahun = $kdTahun ? TahunAjaran::find($kdTahun) : $tahunAjaranAktif;

        $kelasList = Kelas::orderBy('tingkat')->orderBy('nm_kelas')->get();

        $kdKelas = $request->query('kd_kelas');
        $kelasAsal = $kdKelas
            ? $kelasList->firstWhere('kd_kelas', (int) $kdKelas)
            : $kelasList->first();

        $siswaInClass = collect();
        $riwayatEvaluasi = collect();
        $targetKelasNaik = collect();
        $targetKelasTinggal = collect();

        if ($kelasAsal) {
            $siswaInClass = Siswa::where('status_siswa', 'aktif')
                ->where(function ($q) use ($kelasAsal) {
                    $q->whereHas('kenaikanKelas', function ($sub) use ($kelasAsal) {
                        $sub->where('kd_kelas_tujuan', $kelasAsal->kd_kelas)
                            ->whereRaw('kd_kenaikan = (SELECT MAX(k2.kd_kenaikan) FROM kenaikan_kelas k2 WHERE k2.kd_siswa = siswa.kd_siswa)');
                    });

                    if ($kelasAsal->tingkat === 7) {
                        $q->orWhereDoesntHave('kenaikanKelas');
                    }
                })
                ->with([
                    'kenaikanKelas' => function ($q) use ($selectedTahun) {
                        if ($selectedTahun) {
                            $q->where('kd_tahun', $selectedTahun->kd_tahun);
                        }
                    },
                    'kenaikanKelas.kelasTujuan',
                ])
                ->orderBy('nm_siswa')
                ->get();

            if ($selectedTahun) {
                $riwayatEvaluasi = KenaikanKelas::with(['siswa', 'tahunAjaran', 'kelasAsal', 'kelasTujuan'])
                    ->where('kd_tahun', $selectedTahun->kd_tahun)
                    ->where('kd_kelas_asal', $kelasAsal->kd_kelas)
                    ->latest('kd_kenaikan')
                    ->get();
            }

            $targetKelasNaik = $kelasList->where('tingkat', $kelasAsal->tingkat + 1)->values();
            $targetKelasTinggal = $kelasList->where('tingkat', $kelasAsal->tingkat)->values();
        }

        return Inertia::render('Akademik/KenaikanKelas', [
            'tahunAjaranList' => TahunAjaran::orderByDesc('kd_tahun')->get(),
            'selectedTahun' => $selectedTahun,
            'kelasList' => $kelasList,
            'selectedKelas' => $kelasAsal,
            'siswaList' => $siswaInClass,
            'riwayatEvaluasi' => $riwayatEvaluasi,
            'targetKelasNaik' => $targetKelasNaik,
            'targetKelasTinggal' => $targetKelasTinggal,
            'filters' => [
                'kd_tahun' => $selectedTahun?->kd_tahun,
                'kd_kelas' => $kelasAsal?->kd_kelas,
            ],
        ]);
    }

    /**
     * Process bulk or single student evaluation (promote or retain).
     */
    public function process(
        KenaikanKelasRequest $request,
        ProcessKenaikanKelasAction $naikAction,
        ProcessTinggalKelasAction $tinggalAction
    ): RedirectResponse {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $naikAction, $tinggalAction) {
            foreach ($validated['evaluasi'] as $item) {
                if ($item['status'] === 'naik') {
                    $naikAction->execute(
                        (int) $item['kd_siswa'],
                        (int) $validated['kd_tahun'],
                        (int) $validated['kd_kelas_asal'],
                        (int) $item['kd_kelas_tujuan']
                    );
                } else {
                    $tinggalAction->execute(
                        (int) $item['kd_siswa'],
                        (int) $validated['kd_tahun'],
                        (int) $validated['kd_kelas_asal'],
                        (int) $item['kd_kelas_tujuan']
                    );
                }
            }
        });

        $total = count($validated['evaluasi']);

        return back()->with('success', "Berhasil memproses evaluasi akademik untuk {$total} siswa.");
    }

    /**
     * Remove an evaluation record.
     */
    public function destroy(KenaikanKelas $kenaikanKelas): RedirectResponse
    {
        $kenaikanKelas->delete();

        return back()->with('success', 'Catatan evaluasi kenaikan kelas berhasil dihapus.');
    }
}
