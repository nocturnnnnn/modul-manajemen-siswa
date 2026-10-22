<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Card from '../../components/ui/Card.vue';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Select from '../../components/ui/Select.vue';
import DataTable, { type TableHeader } from '../../components/ui/DataTable.vue';
import Pagination from '../../components/ui/Pagination.vue';
import DokumenStatusBadge from '../../components/modules/dokumen/DokumenStatusBadge.vue';
import DokumenVerifyModal from '../../components/modules/dokumen/DokumenVerifyModal.vue';
import DokumenUploadModal from '../../components/modules/dokumen/DokumenUploadModal.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import { debounce } from '../../composables/useDebounce';
import type { DokumenSiswa, JenisDokumen, PaginatedData } from '../../types';

interface SiswaOption {
    kd_siswa: number;
    nis: string;
    nm_siswa: string;
}

interface Stats {
    total: number;
    belum: number;
    terverifikasi: number;
    ditolak: number;
}

const props = defineProps<{
    dokumenList: PaginatedData<DokumenSiswa>;
    jenisDokumenList: JenisDokumen[];
    siswaList: SiswaOption[];
    stats: Stats;
    filters: {
        search: string;
        status: string;
        jenis: string | number;
    };
}>();

const { confirmAction } = useConfirmModal();

// Filters state
const search = ref(props.filters.search);
const status = ref(props.filters.status);
const jenis = ref(props.filters.jenis ? String(props.filters.jenis) : '');

// Modals state
const isUploadOpen = ref(false);
const isVerifyOpen = ref(false);
const selectedDokumen = ref<DokumenSiswa | null>(null);

const headers: TableHeader[] = [
    { key: 'siswa', label: 'Siswa' },
    { key: 'jenis', label: 'Jenis Dokumen' },
    { key: 'nomor', label: 'Nomor Berkas' },
    { key: 'tgl_upload', label: 'Tanggal Upload' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Aksi', align: 'right' },
];

const applyFilter = debounce(() => {
    router.get(
        '/dokumen',
        {
            search: search.value || undefined,
            status: status.value || undefined,
            jenis: jenis.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}, 300);

watch([search, status, jenis], () => {
    applyFilter();
});

function resetFilters() {
    search.value = '';
    status.value = '';
    jenis.value = '';
}

function openVerifyModal(doc: DokumenSiswa) {
    selectedDokumen.value = doc;
    isVerifyOpen.value = true;
}

function handleDelete(doc: DokumenSiswa) {
    confirmAction({
        title: 'Hapus Dokumen Digital',
        message: `Hapus berkas "${doc.jenis?.nm_jenis}" milik "${doc.siswa?.nm_siswa}"? Tindakan ini tidak dapat dibatalkan.`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/dokumen/${doc.kd_dokumen}`);
        },
    });
}
</script>

<template>
    <AppLayout title="Pengelolaan Dokumen Digital">
        <Head title="Pengelolaan Dokumen Digital - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Pengelolaan Dokumen Digital"
            subtitle="Verifikasi berkas persyaratan dan arsip digital siswa SMP Kristen 1 Wonosobo"
            :breadcrumbs="[{ label: 'Dokumen Digital', current: true }]"
        >
            <template #actions>
                <Button
                    variant="primary"
                    size="sm"
                    @click="isUploadOpen = true"
                >
                    <svg
                        class="mr-1.5 -ml-1 h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    Unggah Berkas Baru
                </Button>
            </template>
        </PageHeader>

        <!-- Stats Overview Cards -->
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <Card padding="sm" class="border-l-4 border-l-primary-600">
                <p
                    class="text-2xs font-semibold tracking-wider text-slate-500 uppercase"
                >
                    Total Berkas
                </p>
                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ stats.total }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-amber-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-amber-600 uppercase"
                >
                    Menunggu Verifikasi
                </p>
                <p class="mt-1 text-2xl font-bold text-amber-700">
                    {{ stats.belum }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-emerald-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-emerald-600 uppercase"
                >
                    Terverifikasi (Sah)
                </p>
                <p class="mt-1 text-2xl font-bold text-emerald-700">
                    {{ stats.terverifikasi }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-rose-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-rose-600 uppercase"
                >
                    Ditolak / Revisi
                </p>
                <p class="mt-1 text-2xl font-bold text-rose-700">
                    {{ stats.ditolak }}
                </p>
            </Card>
        </div>

        <!-- Filter Bar Card -->
        <Card padding="md" class="mb-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:items-end">
                <div class="md:col-span-2">
                    <Input
                        v-model="search"
                        label="Pencarian Cepat"
                        placeholder="Cari nama siswa, NIS, atau no. berkas..."
                    />
                </div>

                <div>
                    <Select
                        v-model="status"
                        label="Status Verifikasi"
                        :options="[
                            { label: 'Semua Status', value: '' },
                            { label: 'Menunggu Verifikasi', value: 'belum' },
                            {
                                label: 'Terverifikasi (Sah)',
                                value: 'terverifikasi',
                            },
                            { label: 'Ditolak', value: 'ditolak' },
                        ]"
                    />
                </div>

                <div>
                    <Select
                        v-model="jenis"
                        label="Jenis Dokumen"
                        :options="[
                            { label: 'Semua Jenis', value: '' },
                            ...jenisDokumenList.map((j) => ({
                                label: `${j.nm_jenis} (${j.kode})`,
                                value: String(j.kd_jenis),
                            })),
                        ]"
                    />
                </div>
            </div>

            <div
                v-if="search || status || jenis"
                class="mt-3 flex items-center justify-end"
            >
                <button
                    type="button"
                    class="cursor-pointer text-xs font-semibold text-primary-600 hover:text-primary-800"
                    @click="resetFilters"
                >
                    Reset Filter
                </button>
            </div>
        </Card>

        <!-- Document Data Table -->
        <div class="space-y-4">
            <DataTable
                :headers="headers"
                :items="dokumenList.data"
                empty-message="Tidak ada dokumen digital yang ditemukan."
            >
                <template #default="{ items }">
                    <tr
                        v-for="item in items as DokumenSiswa[]"
                        :key="item.kd_dokumen"
                        class="border-b border-border-subtle transition-colors hover:bg-primary-50/40"
                    >
                        <td class="px-4 py-3.5 text-xs">
                            <p class="font-bold text-slate-900">
                                {{ item.siswa?.nm_siswa }}
                            </p>
                            <p class="text-2xs text-slate-500">
                                NIS: {{ item.siswa?.nis }} • NISN:
                                {{ item.siswa?.nisn }}
                            </p>
                        </td>

                        <td class="px-4 py-3.5 text-xs">
                            <span class="font-semibold text-slate-800">
                                {{ item.jenis?.nm_jenis }}
                            </span>
                            <span
                                class="text-2xs ml-1 rounded bg-slate-100 px-1.5 py-0.5 font-mono text-slate-600"
                            >
                                {{ item.jenis?.kode }}
                            </span>
                        </td>

                        <td
                            class="px-4 py-3.5 font-mono text-xs text-slate-700"
                        >
                            {{ item.nomor_dokumen || '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-xs text-slate-600">
                            {{ item.tgl_upload }}
                        </td>

                        <td class="px-4 py-3.5 text-xs">
                            <DokumenStatusBadge
                                :status="item.status_verifikasi"
                            />
                            <p
                                v-if="
                                    item.status_verifikasi === 'ditolak' &&
                                    item.catatan_verifikasi
                                "
                                class="text-2xs mt-1 max-w-xs truncate text-rose-600"
                                :title="item.catatan_verifikasi"
                            >
                                {{ item.catatan_verifikasi }}
                            </p>
                        </td>

                        <td class="px-4 py-3.5 text-right text-xs">
                            <div class="flex items-center justify-end gap-1.5">
                                <a
                                    :href="`/storage/${item.file_path}`"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <Button variant="outline" size="sm">
                                        Berkas
                                    </Button>
                                </a>

                                <Button
                                    variant="secondary"
                                    size="sm"
                                    @click="openVerifyModal(item)"
                                >
                                    Verifikasi
                                </Button>

                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-rose-600 hover:bg-rose-50"
                                    @click="handleDelete(item)"
                                >
                                    Hapus
                                </Button>
                            </div>
                        </td>
                    </tr>
                </template>
            </DataTable>

            <Pagination
                v-if="dokumenList.links.length > 3"
                :links="dokumenList.links"
                :from="dokumenList.from"
                :to="dokumenList.to"
                :total="dokumenList.total"
            />
        </div>

        <!-- Modals -->
        <DokumenVerifyModal
            :is-open="isVerifyOpen"
            :dokumen="selectedDokumen"
            @close="isVerifyOpen = false"
        />

        <DokumenUploadModal
            :is-open="isUploadOpen"
            :siswa-list="siswaList"
            :jenis-dokumen-list="jenisDokumenList"
            @close="isUploadOpen = false"
        />
    </AppLayout>
</template>
