<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Card from '../../components/ui/Card.vue';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Tabs, { type TabItem } from '../../components/ui/Tabs.vue';
import DataTable, { type TableHeader } from '../../components/ui/DataTable.vue';
import Pagination from '../../components/ui/Pagination.vue';
import MutasiFormModal from '../../components/modules/mutasi/MutasiFormModal.vue';
import PindahSekolahModal from '../../components/modules/mutasi/PindahSekolahModal.vue';
import RiwayatMutasiTable from '../../components/modules/mutasi/RiwayatMutasiTable.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import { debounce } from '../../composables/useDebounce';
import type { Mutasi, PaginatedData, PindahSekolah, Siswa } from '../../types';

interface Stats {
    totalMutasi: number;
    totalPindah: number;
    mutasiMasuk: number;
    mutasiKeluar: number;
}

const props = defineProps<{
    mutasiList: PaginatedData<Mutasi>;
    pindahList: PaginatedData<PindahSekolah>;
    siswaList: Array<{ kd_siswa: number; nis: string; nm_siswa: string }>;
    stats: Stats;
    filters: {
        search: string;
        tab: string;
    };
}>();

const { confirmAction } = useConfirmModal();

const search = ref(props.filters.search);
const activeTab = ref(props.filters.tab || 'mutasi');

const isMutasiModalOpen = ref(false);
const isPindahModalOpen = ref(false);

const tabs: TabItem[] = [
    { id: 'mutasi', label: `Riwayat Mutasi (${props.stats.totalMutasi})` },
    { id: 'pindah', label: `Pindah Sekolah (${props.stats.totalPindah})` },
];

const pindahHeaders: TableHeader[] = [
    { key: 'siswa', label: 'Nama Siswa & NIS' },
    { key: 'sekolah_tujuan', label: 'Sekolah Tujuan' },
    { key: 'tgl_pindah', label: 'Tanggal Pindah' },
    { key: 'alasan', label: 'Alasan Kepindahan' },
    { key: 'actions', label: 'Aksi', align: 'right' },
];

const applyFilter = debounce(() => {
    router.get(
        '/mutasi',
        {
            search: search.value || undefined,
            tab: activeTab.value,
        },
        { preserveState: true, replace: true },
    );
}, 300);

watch(search, () => {
    applyFilter();
});

function handleTabChange(tabId: string) {
    activeTab.value = tabId;
    router.get(
        '/mutasi',
        {
            search: search.value || undefined,
            tab: tabId,
        },
        { preserveState: true, replace: true },
    );
}

function handleDeleteMutasi(item: Mutasi) {
    confirmAction({
        title: 'Hapus Catatan Mutasi',
        message: `Hapus catatan surat mutasi untuk "${item.siswa?.nm_siswa}"? Jika status siswa saat ini mutasi, status akan dipulihkan ke aktif.`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/mutasi/${item.kd_mutasi}`);
        },
    });
}

function handleDeletePindah(item: PindahSekolah) {
    confirmAction({
        title: 'Hapus Catatan Pindah Sekolah',
        message: `Hapus catatan kepindahan sekolah "${item.siswa?.nm_siswa}" ke "${item.sekolah_tujuan}"? Status siswa akan dipulihkan ke aktif.`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/mutasi/pindah/${item.kd_pindah}`);
        },
    });
}
</script>

<template>
    <AppLayout title="Mutasi & Pindah Sekolah">
        <Head title="Mutasi & Pindah Sekolah - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Mutasi & Pindah Sekolah"
            subtitle="Pencatatan mutasi masuk, keluar, internal, dan arsip kepindahan sekolah siswa"
            :breadcrumbs="[{ label: 'Mutasi & Pindah', current: true }]"
        >
            <template #actions>
                <Button
                    variant="outline"
                    size="sm"
                    @click="isPindahModalOpen = true"
                >
                    + Catat Pindah Sekolah
                </Button>
                <Button
                    variant="primary"
                    size="sm"
                    @click="isMutasiModalOpen = true"
                >
                    + Catat Surat Mutasi
                </Button>
            </template>
        </PageHeader>

        <!-- Stats Overview Cards -->
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <Card padding="sm" class="border-l-4 border-l-primary-600">
                <p
                    class="text-2xs font-semibold tracking-wider text-slate-500 uppercase"
                >
                    Total Surat Mutasi
                </p>
                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ stats.totalMutasi }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-rose-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-rose-600 uppercase"
                >
                    Siswa Pindah Sekolah
                </p>
                <p class="mt-1 text-2xl font-bold text-rose-700">
                    {{ stats.totalPindah }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-emerald-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-emerald-600 uppercase"
                >
                    Mutasi Masuk
                </p>
                <p class="mt-1 text-2xl font-bold text-emerald-700">
                    {{ stats.mutasiMasuk }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-amber-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-amber-600 uppercase"
                >
                    Mutasi Keluar
                </p>
                <p class="mt-1 text-2xl font-bold text-amber-700">
                    {{ stats.mutasiKeluar }}
                </p>
            </Card>
        </div>

        <!-- Filter Bar Card -->
        <Card padding="md" class="mb-6">
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="w-full sm:max-w-md">
                    <Input
                        v-model="search"
                        placeholder="Cari siswa, no. surat, atau sekolah tujuan..."
                    />
                </div>
                <div v-if="search">
                    <button
                        type="button"
                        class="cursor-pointer text-xs font-semibold text-primary-600 hover:text-primary-800"
                        @click="search = ''"
                    >
                        Reset Pencarian
                    </button>
                </div>
            </div>
        </Card>

        <!-- Tabs & Data Tables -->
        <Card padding="md">
            <template #header>
                <Tabs
                    :tabs="tabs"
                    :active-tab="activeTab"
                    @update:active-tab="handleTabChange"
                />
            </template>

            <!-- Tab 1: Mutasi -->
            <div v-show="activeTab === 'mutasi'" class="space-y-4 py-2">
                <RiwayatMutasiTable
                    :items="mutasiList.data"
                    @delete="handleDeleteMutasi"
                />

                <Pagination
                    v-if="mutasiList.links.length > 3"
                    :links="mutasiList.links"
                    :from="mutasiList.from"
                    :to="mutasiList.to"
                    :total="mutasiList.total"
                />
            </div>

            <!-- Tab 2: Pindah Sekolah -->
            <div v-show="activeTab === 'pindah'" class="space-y-4 py-2">
                <DataTable
                    :headers="pindahHeaders"
                    :items="pindahList.data"
                    empty-message="Belum ada data siswa yang pindah sekolah keluar."
                >
                    <template #default="{ items: rows }">
                        <tr
                            v-for="item in rows as PindahSekolah[]"
                            :key="item.kd_pindah"
                            class="border-b border-border-subtle transition-colors hover:bg-primary-50/40"
                        >
                            <td class="px-4 py-3.5 text-xs">
                                <p class="font-bold text-slate-900">
                                    {{ item.siswa?.nm_siswa }}
                                </p>
                                <p class="text-2xs text-slate-500">
                                    NIS: {{ item.siswa?.nis }}
                                </p>
                            </td>

                            <td
                                class="px-4 py-3.5 text-xs font-semibold text-slate-800"
                            >
                                {{ item.sekolah_tujuan }}
                            </td>

                            <td class="px-4 py-3.5 text-xs text-slate-600">
                                {{ item.tgl_pindah }}
                            </td>

                            <td
                                class="max-w-xs truncate px-4 py-3.5 text-xs text-slate-600"
                                :title="item.alasan"
                            >
                                {{ item.alasan }}
                            </td>

                            <td class="px-4 py-3.5 text-right text-xs">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-rose-600 hover:bg-rose-50"
                                    @click="handleDeletePindah(item)"
                                >
                                    Hapus
                                </Button>
                            </td>
                        </tr>
                    </template>
                </DataTable>

                <Pagination
                    v-if="pindahList.links.length > 3"
                    :links="pindahList.links"
                    :from="pindahList.from"
                    :to="pindahList.to"
                    :total="pindahList.total"
                />
            </div>
        </Card>

        <!-- Modals -->
        <MutasiFormModal
            :show="isMutasiModalOpen"
            :siswa-list="siswaList"
            @close="isMutasiModalOpen = false"
        />

        <PindahSekolahModal
            :show="isPindahModalOpen"
            :siswa-list="siswaList"
            @close="isPindahModalOpen = false"
        />
    </AppLayout>
</template>
