<?php

namespace App\Http\Controllers;

use App\Actions\Dokumen\UploadDokumenAction;
use App\Actions\Dokumen\VerifyDokumenAction;
use App\Http\Requests\Dokumen\UploadDokumenRequest;
use App\Http\Requests\Dokumen\VerifyDokumenRequest;
use App\Models\DokumenSiswa;
use App\Models\JenisDokumen;
use App\Models\Siswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DokumenSiswaController extends Controller
{
    /**
     * Display a listing of digital documents awaiting review or archived.
     */
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $kdJenis = $request->query('jenis');

        $query = DokumenSiswa::with(['siswa', 'jenis'])
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nomor_dokumen', 'like', "%{$search}%")
                        ->orWhereHas('siswa', function ($sq) use ($search) {
                            $sq->where('nm_siswa', 'like', "%{$search}%")
                                ->orWhere('nis', 'like', "%{$search}%")
                                ->orWhere('nisn', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status && in_array($status, ['belum', 'terverifikasi', 'ditolak']), function ($q) use ($status) {
                $q->where('status_verifikasi', $status);
            })
            ->when($kdJenis, function ($q, $kdJenis) {
                $q->where('kd_jenis', $kdJenis);
            })
            ->latest('kd_dokumen');

        $stats = [
            'total' => DokumenSiswa::count(),
            'belum' => DokumenSiswa::where('status_verifikasi', 'belum')->count(),
            'terverifikasi' => DokumenSiswa::where('status_verifikasi', 'terverifikasi')->count(),
            'ditolak' => DokumenSiswa::where('status_verifikasi', 'ditolak')->count(),
        ];

        return Inertia::render('Dokumen/Index', [
            'dokumenList' => $query->paginate(10)->withQueryString(),
            'jenisDokumenList' => JenisDokumen::orderBy('nm_jenis')->get(),
            'siswaList' => Siswa::select('kd_siswa', 'nis', 'nm_siswa')
                ->where('status_siswa', 'aktif')
                ->orderBy('nm_siswa')
                ->get(),
            'stats' => $stats,
            'filters' => [
                'search' => $search ?? '',
                'status' => $status ?? '',
                'jenis' => $kdJenis ?? '',
            ],
        ]);
    }

    /**
     * Upload and store a new student digital document.
     */
    public function upload(UploadDokumenRequest $request, UploadDokumenAction $action): RedirectResponse
    {
        $action->execute($request->validated(), $request->file('file'));

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    /**
     * Verify or reject student digital document.
     */
    public function verify(
        VerifyDokumenRequest $request,
        DokumenSiswa $dokumenSiswa,
        VerifyDokumenAction $action
    ): RedirectResponse {
        $action->execute(
            $dokumenSiswa,
            $request->validated('status_verifikasi'),
            $request->validated('catatan_verifikasi')
        );

        $statusLabel = $request->validated('status_verifikasi') === 'terverifikasi' ? 'disetujui' : 'ditolak';

        return back()->with('success', "Dokumen berhasil {$statusLabel}.");
    }

    /**
     * Remove digital document and its stored file.
     */
    public function destroy(DokumenSiswa $dokumenSiswa): RedirectResponse
    {
        if ($dokumenSiswa->file_path && Storage::disk('public')->exists($dokumenSiswa->file_path)) {
            Storage::disk('public')->delete($dokumenSiswa->file_path);
        }

        $dokumenSiswa->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
