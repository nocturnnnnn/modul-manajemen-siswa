<script setup lang="ts">
import Badge from '../../ui/Badge.vue';

defineProps<{
    reportType: string;
    reportData: {
        title: string;
        count: number;
        rows: Array<Record<string, any>>;
    };
    isPrintMode?: boolean;
}>();
</script>

<template>
    <div
        class="border-surface-200 overflow-hidden rounded-xl border bg-white shadow-xs print:border-none print:shadow-none"
    >
        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table
                class="text-surface-700 w-full text-left text-xs print:text-black"
            >
                <thead
                    class="border-surface-200 bg-surface-50 text-surface-500 border-b font-semibold tracking-wider uppercase print:border-b-2 print:border-black print:bg-white print:text-black"
                >
                    <!-- Master Siswa Headers -->
                    <tr v-if="reportType === 'master_siswa'">
                        <th scope="col" class="px-4 py-3 text-center">No</th>
                        <th scope="col" class="px-4 py-3">NIS / NISN</th>
                        <th scope="col" class="px-4 py-3">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3 text-center">L/P</th>
                        <th scope="col" class="px-4 py-3">Agama</th>
                        <th scope="col" class="px-4 py-3">Alamat</th>
                        <th scope="col" class="px-4 py-3">Jalur Daftar</th>
                        <th scope="col" class="px-4 py-3 text-center">
                            Status
                        </th>
                    </tr>

                    <!-- Rombel Kelas Headers -->
                    <tr v-else-if="reportType === 'rombel_kelas'">
                        <th scope="col" class="px-4 py-3 text-center">No</th>
                        <th scope="col" class="px-4 py-3">NIS</th>
                        <th scope="col" class="px-4 py-3">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3 text-center">L/P</th>
                        <th scope="col" class="px-4 py-3">Agama</th>
                        <th scope="col" class="px-4 py-3">Kelas Asal</th>
                        <th scope="col" class="px-4 py-3">Kelas Tujuan</th>
                        <th scope="col" class="px-4 py-3 text-center">
                            Evaluasi
                        </th>
                    </tr>

                    <!-- Mutasi Headers -->
                    <tr v-else-if="reportType === 'mutasi'">
                        <th scope="col" class="px-4 py-3 text-center">No</th>
                        <th scope="col" class="px-4 py-3">Tanggal</th>
                        <th scope="col" class="px-4 py-3">NIS</th>
                        <th scope="col" class="px-4 py-3">Nama Siswa</th>
                        <th scope="col" class="px-4 py-3">Jenis Mutasi</th>
                        <th scope="col" class="px-4 py-3">
                            Keterangan / Surat / Alasan
                        </th>
                    </tr>

                    <!-- Kelulusan & Alumni Headers -->
                    <tr v-else-if="reportType === 'kelulusan_alumni'">
                        <th scope="col" class="px-4 py-3 text-center">No</th>
                        <th scope="col" class="px-4 py-3">NIS</th>
                        <th scope="col" class="px-4 py-3">Nama Lengkap</th>
                        <th scope="col" class="px-4 py-3">Tahun Lulus</th>
                        <th scope="col" class="px-4 py-3">Nomor Ijazah</th>
                        <th scope="col" class="px-4 py-3">Kegiatan Lanjutan</th>
                        <th scope="col" class="px-4 py-3">Nama Instansi</th>
                        <th scope="col" class="px-4 py-3">Kontak</th>
                    </tr>
                </thead>

                <tbody
                    class="divide-surface-200 divide-y print:divide-slate-300"
                >
                    <tr
                        v-for="(row, idx) in reportData.rows"
                        :key="idx"
                        class="hover:bg-surface-50 transition-colors print:hover:bg-white"
                    >
                        <!-- Master Siswa Row -->
                        <template v-if="reportType === 'master_siswa'">
                            <td
                                class="text-surface-400 px-4 py-3 text-center font-medium print:text-black"
                            >
                                {{ idx + 1 }}
                            </td>
                            <td
                                class="text-surface-600 px-4 py-3 font-mono whitespace-nowrap print:text-black"
                            >
                                {{ row.nis }}
                                <span class="text-surface-400 print:text-black"
                                    >/ {{ row.nisn }}</span
                                >
                            </td>
                            <td
                                class="text-surface-900 px-4 py-3 font-semibold print:text-black"
                            >
                                {{ row.nm_siswa }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{
                                    row.jenis_kelamin === 'Laki-laki'
                                        ? 'L'
                                        : 'P'
                                }}
                            </td>
                            <td class="px-4 py-3">{{ row.agama }}</td>
                            <td class="max-w-xs truncate px-4 py-3">
                                {{ row.alamat }}
                            </td>
                            <td class="px-4 py-3">{{ row.jalur }}</td>
                            <td class="px-4 py-3 text-center">
                                <Badge
                                    :variant="
                                        row.status === 'Aktif'
                                            ? 'success'
                                            : 'neutral'
                                    "
                                    size="sm"
                                >
                                    {{ row.status }}
                                </Badge>
                            </td>
                        </template>

                        <!-- Rombel Kelas Row -->
                        <template v-else-if="reportType === 'rombel_kelas'">
                            <td
                                class="text-surface-400 px-4 py-3 text-center font-medium print:text-black"
                            >
                                {{ idx + 1 }}
                            </td>
                            <td
                                class="text-surface-600 px-4 py-3 font-mono whitespace-nowrap print:text-black"
                            >
                                {{ row.nis }}
                            </td>
                            <td
                                class="text-surface-900 px-4 py-3 font-semibold print:text-black"
                            >
                                {{ row.nm_siswa }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{
                                    row.jenis_kelamin === 'Laki-laki'
                                        ? 'L'
                                        : 'P'
                                }}
                            </td>
                            <td class="px-4 py-3">{{ row.agama }}</td>
                            <td class="px-4 py-3">{{ row.kelas_asal }}</td>
                            <td
                                class="text-surface-900 px-4 py-3 font-medium print:text-black"
                            >
                                {{ row.kelas_tujuan }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <Badge
                                    :variant="
                                        row.status === 'Naik Kelas'
                                            ? 'success'
                                            : 'danger'
                                    "
                                    size="sm"
                                >
                                    {{ row.status }}
                                </Badge>
                            </td>
                        </template>

                        <!-- Mutasi Row -->
                        <template v-else-if="reportType === 'mutasi'">
                            <td
                                class="text-surface-400 px-4 py-3 text-center font-medium print:text-black"
                            >
                                {{ idx + 1 }}
                            </td>
                            <td class="px-4 py-3 font-medium whitespace-nowrap">
                                {{ row.tanggal }}
                            </td>
                            <td
                                class="text-surface-600 px-4 py-3 font-mono whitespace-nowrap print:text-black"
                            >
                                {{ row.nis }}
                            </td>
                            <td
                                class="text-surface-900 px-4 py-3 font-semibold print:text-black"
                            >
                                {{ row.nm_siswa }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <Badge
                                    :variant="
                                        row.jenis.includes('Keluar') ||
                                        row.jenis.includes('Pindah')
                                            ? 'warning'
                                            : 'primary'
                                    "
                                    size="sm"
                                >
                                    {{ row.jenis }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3">{{ row.keterangan }}</td>
                        </template>

                        <!-- Kelulusan & Alumni Row -->
                        <template v-else-if="reportType === 'kelulusan_alumni'">
                            <td
                                class="text-surface-400 px-4 py-3 text-center font-medium print:text-black"
                            >
                                {{ idx + 1 }}
                            </td>
                            <td
                                class="text-surface-600 px-4 py-3 font-mono whitespace-nowrap print:text-black"
                            >
                                {{ row.nis }}
                            </td>
                            <td
                                class="text-surface-900 px-4 py-3 font-semibold print:text-black"
                            >
                                {{ row.nm_siswa }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ row.tahun_lulus }}
                            </td>
                            <td class="px-4 py-3 font-mono whitespace-nowrap">
                                {{ row.no_ijazah }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <Badge
                                    :variant="
                                        row.kegiatan_lanjutan.includes('SMA')
                                            ? 'primary'
                                            : 'secondary'
                                    "
                                    size="sm"
                                >
                                    {{ row.kegiatan_lanjutan }}
                                </Badge>
                            </td>
                            <td
                                class="text-surface-900 px-4 py-3 font-medium print:text-black"
                            >
                                {{ row.instansi }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ row.kontak }}
                            </td>
                        </template>
                    </tr>

                    <tr v-if="reportData.rows.length === 0">
                        <td
                            colspan="8"
                            class="text-surface-400 px-6 py-12 text-center"
                        >
                            Tidak ada data laporan yang sesuai dengan kriteria
                            filter terpilih.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
