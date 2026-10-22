<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Button from '../../components/ui/Button.vue';
import Card from '../../components/ui/Card.vue';
import LaporanFilterCard from '../../components/modules/laporan/LaporanFilterCard.vue';
import LaporanPreviewTable from '../../components/modules/laporan/LaporanPreviewTable.vue';
import type { Agama, Kelas, TahunAjaran } from '../../types/models';

const props = defineProps<{
    reportType: string;
    filters: Record<string, any>;
    reportData: {
        title: string;
        count: number;
        rows: Array<Record<string, any>>;
    };
    kelasList: Kelas[];
    tahunAjaranList: TahunAjaran[];
    agamaList: Agama[];
}>();

function handleTypeChange(type: string) {
    router.get('/laporan', { type }, { preserveState: false });
}

function handleApplyFilter(newFilters: Record<string, any>) {
    router.get('/laporan', newFilters, { preserveState: true });
}

function handleReset() {
    router.get(
        '/laporan',
        { type: props.reportType },
        { preserveState: false },
    );
}

function handleOpenCetak() {
    const params = new URLSearchParams();
    params.set('type', props.reportType);
    for (const [key, val] of Object.entries(props.filters)) {
        if (val && key !== 'type') {
            params.set(key, String(val));
        }
    }
    window.open(`/laporan/cetak?${params.toString()}`, '_blank');
}
</script>

<template>
    <AppLayout>
        <Head :title="`${reportData.title} - Laporan`" />

        <div class="space-y-6">
            <PageHeader
                title="Pusat Laporan & Cetak Rekapitulasi Siswa"
                description="Rekapitulasi data master siswa, rombel kelas, mutasi, kelulusan, dan direktori alumni dengan format cetak ramah printer."
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Laporan & Rekapitulasi' },
                ]"
            >
                <template #actions>
                    <Button variant="primary" @click="handleOpenCetak">
                        <svg
                            class="mr-1.5 h-4 w-4"
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
                        Cetak Laporan Resmi
                    </Button>
                </template>
            </PageHeader>

            <!-- Filter Card -->
            <LaporanFilterCard
                :report-type="reportType"
                :filters="filters"
                :kelas-list="kelasList"
                :tahun-ajaran-list="tahunAjaranList"
                :agama-list="agamaList"
                @change-type="handleTypeChange"
                @filter="handleApplyFilter"
                @reset="handleReset"
            />

            <!-- Preview Header & Stats Summary -->
            <Card>
                <div
                    class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h3 class="text-surface-900 text-base font-bold">
                            {{ reportData.title }}
                        </h3>
                        <p class="text-surface-500 text-xs">
                            Menampilkan {{ reportData.count }} baris data
                            berdasarkan kriteria filter aktif saat ini.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <Button
                            size="sm"
                            variant="outline"
                            @click="handleOpenCetak"
                        >
                            Mode Cetak Bersih
                        </Button>
                    </div>
                </div>
            </Card>

            <!-- Table Preview -->
            <LaporanPreviewTable
                :report-type="reportType"
                :report-data="reportData"
            />
        </div>
    </AppLayout>
</template>
