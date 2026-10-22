<?php

namespace App\Http\Controllers;

use App\Actions\Mutasi\CatatMutasiAction;
use App\Actions\Mutasi\CatatPindahSekolahAction;
use App\Http\Requests\Mutasi\CatatMutasiRequest;
use App\Http\Requests\Mutasi\CatatPindahSekolahRequest;
use App\Models\Mutasi;
use App\Models\PindahSekolah;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MutasiController extends Controller
{
    /**
     * Display a listing of mutasi and transfer records.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();
        $tab = $request->query('tab', 'mutasi');

        $mutasiList = Mutasi::with('siswa')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('no_surat', 'like', "%{$search}%")
                    ->orWhereHas('siswa', function ($sq) use ($search) {
                        $sq->where('nm_siswa', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            })
            ->latest('kd_mutasi')
            ->paginate(10, ['*'], 'mutasi_page')
            ->withQueryString();

        $pindahList = PindahSekolah::with('siswa')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('sekolah_tujuan', 'like', "%{$search}%")
                    ->orWhere('alasan', 'like', "%{$search}%")
                    ->orWhereHas('siswa', function ($sq) use ($search) {
                        $sq->where('nm_siswa', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            })
            ->latest('kd_pindah')
            ->paginate(10, ['*'], 'pindah_page')
            ->withQueryString();

        $stats = [
            'totalMutasi' => Mutasi::count(),
            'totalPindah' => PindahSekolah::count(),
            'mutasiMasuk' => Mutasi::where('jenis_mutasi', 'Masuk')->count(),
            'mutasiKeluar' => Mutasi::where('jenis_mutasi', 'Keluar')->count(),
        ];

        return Inertia::render('Mutasi/Index', [
            'mutasiList' => $mutasiList,
            'pindahList' => $pindahList,
            'siswaList' => Siswa::select('kd_siswa', 'nis', 'nm_siswa', 'status_siswa')
                ->whereIn('status_siswa', ['aktif', 'mutasi'])
                ->orderBy('nm_siswa')
                ->get(),
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'tab' => $tab,
            ],
        ]);
    }

    /**
     * Store a new student mutation record.
     */
    public function storeMutasi(CatatMutasiRequest $request, CatatMutasiAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Surat mutasi siswa berhasil dicatat.');
    }

    /**
     * Store a new school transfer record.
     */
    public function storePindah(CatatPindahSekolahRequest $request, CatatPindahSekolahAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return back()->with('success', 'Catatan pindah sekolah berhasil disimpan.');
    }

    /**
     * Remove a mutation record.
     */
    public function destroyMutasi(Mutasi $mutasi): RedirectResponse
    {
        $siswa = $mutasi->siswa;
        $mutasi->delete();

        if ($siswa && $siswa->status_siswa === 'mutasi') {
            $hasOtherOutgoing = Mutasi::where('kd_siswa', $siswa->kd_siswa)
                ->where('jenis_mutasi', 'Keluar')
                ->exists();

            if (! $hasOtherOutgoing) {
                $siswa->update(['status_siswa' => 'aktif']);
            }
        }

        return back()->with('success', 'Catatan mutasi berhasil dihapus.');
    }

    /**
     * Remove a school transfer record.
     */
    public function destroyPindah(PindahSekolah $pindahSekolah): RedirectResponse
    {
        $siswa = $pindahSekolah->siswa;
        $pindahSekolah->delete();

        if ($siswa && $siswa->status_siswa === 'pindah') {
            $hasOtherTransfer = PindahSekolah::where('kd_siswa', $siswa->kd_siswa)->exists();

            if (! $hasOtherTransfer) {
                $siswa->update(['status_siswa' => 'aktif']);
            }
        }

        return back()->with('success', 'Catatan pindah sekolah berhasil dihapus.');
    }
}
