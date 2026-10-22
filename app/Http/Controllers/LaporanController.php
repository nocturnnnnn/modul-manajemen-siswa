<?php

namespace App\Http\Controllers;

use App\Models\Agama;
use App\Models\Kelas;
use App\Models\Kelulusan;
use App\Models\KenaikanKelas;
use App\Models\Mutasi;
use App\Models\PindahSekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    /**
     * Display the main reporting portal with filters, data preview, and print capabilities.
     */
    public function index(Request $request): Response
    {
        $reportType = $request->query('type', 'master_siswa');
        $filters = $request->all();

        $data = $this->queryReportData($reportType, $filters);

        return Inertia::render('Laporan/Index', [
            'reportType' => $reportType,
            'filters' => $filters,
            'reportData' => $data,
            'kelasList' => Kelas::orderBy('tingkat')->orderBy('nm_kelas')->get(),
            'tahunAjaranList' => TahunAjaran::orderByDesc('kd_tahun')->get(),
            'agamaList' => Agama::orderBy('nm_agama')->get(),
        ]);
    }

    /**
     * Render the clean, printer-friendly document layout with official school letterhead.
     */
    public function cetak(Request $request): Response
    {
        $reportType = $request->query('type', 'master_siswa');
        $filters = $request->all();

        $data = $this->queryReportData($reportType, $filters);
        $tahunAktif = TahunAjaran::where('aktif', true)->first();

        return Inertia::render('Laporan/Cetak', [
            'reportType' => $reportType,
            'filters' => $filters,
            'reportData' => $data,
            'tahunAktif' => $tahunAktif,
            'tanggalCetak' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }

    /**
     * Fetch report rows based on report type and applied filter parameters.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    private function queryReportData(string $reportType, array $filters): array
    {
        switch ($reportType) {
            case 'rombel_kelas':
                $kdKelas = $filters['kd_kelas'] ?? null;
                $kdTahun = $filters['kd_tahun'] ?? null;

                $query = KenaikanKelas::with(['siswa.agama', 'kelasAsal', 'kelasTujuan', 'tahunAjaran'])
                    ->when($kdKelas, fn ($q) => $q->where('kd_kelas_tujuan', $kdKelas))
                    ->when($kdTahun, fn ($q) => $q->where('kd_tahun', $kdTahun));

                $items = $query->get()->map(function (KenaikanKelas $k) {
                    return [
                        'nis' => $k->siswa?->nis,
                        'nisn' => $k->siswa?->nisn,
                        'nm_siswa' => $k->siswa?->nm_siswa,
                        'jenis_kelamin' => $k->siswa?->jenis_kelamin,
                        'agama' => $k->siswa?->agama->nm_agama ?? '-',
                        'kelas_asal' => $k->kelasAsal?->nm_kelas,
                        'kelas_tujuan' => $k->kelasTujuan?->nm_kelas,
                        'status' => $k->status === 'naik' ? 'Naik Kelas' : 'Tinggal Kelas',
                        'tahun_ajaran' => $k->tahunAjaran?->nm_tahun,
                    ];
                });

                return [
                    'title' => 'Laporan Pembagian Siswa per Rombel Kelas',
                    'count' => $items->count(),
                    'rows' => $items,
                ];

            case 'mutasi':
                $jenis = $filters['jenis_mutasi'] ?? null;

                $mutasiRows = collect();
                if (! $jenis || $jenis !== 'pindah') {
                    $mQuery = Mutasi::with('siswa')
                        ->when($jenis, fn ($q) => $q->where('jenis_mutasi', $jenis));

                    $mutasiRows = $mQuery->get()->map(function (Mutasi $m) {
                        return [
                            'tanggal' => $m->tgl_mutasi,
                            'nis' => $m->siswa?->nis,
                            'nm_siswa' => $m->siswa?->nm_siswa,
                            'jenis' => "Mutasi {$m->jenis_mutasi}",
                            'keterangan' => "No. Surat: {$m->no_surat}",
                        ];
                    });
                }

                $pindahRows = collect();
                if (! $jenis || $jenis === 'pindah') {
                    $pindahRows = PindahSekolah::with('siswa')->get()->map(function (PindahSekolah $p) {
                        return [
                            'tanggal' => $p->tgl_pindah,
                            'nis' => $p->siswa?->nis,
                            'nm_siswa' => $p->siswa?->nm_siswa,
                            'jenis' => 'Pindah Sekolah',
                            'keterangan' => "Tujuan: {$p->sekolah_tujuan} (Alasan: {$p->alasan})",
                        ];
                    });
                }

                $merged = $mutasiRows->concat($pindahRows)->sortByDesc('tanggal')->values();

                return [
                    'title' => 'Laporan Rekapitulasi Mutasi & Pindah Sekolah',
                    'count' => $merged->count(),
                    'rows' => $merged,
                ];

            case 'kelulusan_alumni':
                $kdTahun = $filters['kd_tahun'] ?? null;
                $kegiatan = $filters['kegiatan_lanjutan'] ?? null;

                $query = Kelulusan::with(['siswa', 'tahunAjaran', 'alumni'])
                    ->when($kdTahun, fn ($q) => $q->where('kd_tahun', $kdTahun))
                    ->when($kegiatan, function ($q) use ($kegiatan) {
                        $q->whereHas('alumni', fn ($aq) => $aq->where('kegiatan_lanjutan', $kegiatan));
                    });

                $items = $query->get()->map(function (Kelulusan $k) {
                    return [
                        'nis' => $k->siswa?->nis,
                        'nm_siswa' => $k->siswa?->nm_siswa,
                        'tahun_lulus' => $k->tahunAjaran?->nm_tahun,
                        'tgl_lulus' => $k->tgl_lulus,
                        'no_ijazah' => $k->no_ijazah ?? 'Belum Dicatat',
                        'kegiatan_lanjutan' => $k->alumni->kegiatan_lanjutan ?? 'Belum Terdata',
                        'instansi' => $k->alumni->instansi ?? '-',
                        'kontak' => $k->alumni->kontak ?? '-',
                    ];
                });

                return [
                    'title' => 'Laporan Rekapitulasi Kelulusan & Penelusuran Alumni',
                    'count' => $items->count(),
                    'rows' => $items,
                ];

            case 'master_siswa':
            default:
                $status = $filters['status_siswa'] ?? null;
                $kdAgama = $filters['kd_agama'] ?? null;
                $gender = $filters['jenis_kelamin'] ?? null;

                $query = Siswa::with(['agama', 'pendaftaran'])
                    ->when($status, fn ($q) => $q->where('status_siswa', $status))
                    ->when($kdAgama, fn ($q) => $q->where('kd_agama', $kdAgama))
                    ->when($gender, fn ($q) => $q->where('jenis_kelamin', $gender))
                    ->orderBy('nm_siswa');

                $items = $query->get()->map(function (Siswa $s) {
                    $pendaftar = $s->pendaftaran->first();

                    return [
                        'nis' => $s->nis,
                        'nisn' => $s->nisn,
                        'nm_siswa' => $s->nm_siswa,
                        'jenis_kelamin' => $s->jenis_kelamin,
                        'agama' => $s->agama->nm_agama ?? '-',
                        'alamat' => $s->alamat,
                        'jalur' => $pendaftar->jalur ?? '-',
                        'asal_sekolah' => $pendaftar->asal_sekolah ?? '-',
                        'status' => ucfirst($s->status_siswa),
                    ];
                });

                return [
                    'title' => 'Laporan Master Data Siswa',
                    'count' => $items->count(),
                    'rows' => $items,
                ];
        }
    }
}
