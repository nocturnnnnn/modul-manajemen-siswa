<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CetakHeader from '../../components/modules/laporan/CetakHeader.vue';
import LaporanPreviewTable from '../../components/modules/laporan/LaporanPreviewTable.vue';
import Button from '../../components/ui/Button.vue';
import type { TahunAjaran } from '../../types/models';

defineProps<{
    reportType: string;
    filters: Record<string, any>;
    reportData: {
        title: string;
        count: number;
        rows: Array<Record<string, any>>;
    };
    tahunAktif: TahunAjaran | null;
    tanggalCetak: string;
}>();

function handlePrint() {
    window.print();
}

function handleClose() {
    window.close();
}
</script>

<template>
    <div
        class="min-h-screen bg-slate-100 p-4 font-sans text-slate-900 print:bg-white print:p-0"
    >
        <Head :title="`Cetak - ${reportData.title}`" />

        <!-- Top Floating Action Toolbar (Hidden during printing) -->
        <div
            class="border-surface-200 mx-auto mb-6 flex max-w-5xl items-center justify-between rounded-xl border bg-white p-4 shadow-sm print:hidden"
        >
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-700"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                        />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Mode Pratinjau Dokumen Cetak
                    </h3>
                    <p class="text-xs text-slate-500">
                        Klik tombol di samping untuk mencetak atau menyimpan ke
                        format PDF.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Button variant="primary" size="sm" @click="handlePrint">
                    Cetak Dokumen (Ctrl + P)
                </Button>
                <Button variant="ghost" size="sm" @click="handleClose">
                    Tutup
                </Button>
            </div>
        </div>

        <!-- Printable Document Sheet (A4 format) -->
        <div
            class="mx-auto max-w-5xl rounded-2xl border border-slate-200 bg-white p-8 shadow-md print:max-w-none print:border-none print:p-0 print:shadow-none"
        >
            <!-- Official School Letterhead -->
            <CetakHeader
                :title="reportData.title"
                :tahun-ajaran="
                    tahunAktif
                        ? `${tahunAktif.nm_tahun} (${tahunAktif.semester})`
                        : undefined
                "
                :tanggal-cetak="tanggalCetak"
            />

            <!-- Table Rows -->
            <LaporanPreviewTable
                :report-type="reportType"
                :report-data="reportData"
                :is-print-mode="true"
            />

            <!-- Document Signatures Footer -->
            <div
                class="mt-12 grid grid-cols-2 gap-8 text-xs text-slate-800 print:text-black"
            >
                <div class="text-center">
                    <p class="text-slate-500 print:text-black">Mengetahui,</p>
                    <p class="font-semibold">Kepala Tata Usaha</p>
                    <div class="h-20" />
                    <p class="font-bold underline">
                        Dra. Maria Christiani, M.Pd.
                    </p>
                    <p class="text-2xs text-slate-500 print:text-black">
                        NIP. 19780512 200501 2 003
                    </p>
                </div>

                <div class="text-center">
                    <p class="text-slate-500 print:text-black">
                        Wonosobo, {{ tanggalCetak }}
                    </p>
                    <p class="font-semibold">Kepala SMP Kristen 1 Wonosobo</p>
                    <div class="h-20" />
                    <p class="font-bold underline">
                        Yohanes Handoko, S.Pd., M.M.
                    </p>
                    <p class="text-2xs text-slate-500 print:text-black">
                        NIP. 19720315 199802 1 004
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    @page {
        size: A4 portrait;
        margin: 1.5cm;
    }
    body {
        background: white !important;
        color: black !important;
    }
}
</style>
