<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Card from '../../components/ui/Card.vue';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Select from '../../components/ui/Select.vue';
import KelulusanTable from '../../components/modules/kelulusan/KelulusanTable.vue';
import VerifikasiKelulusanModal from '../../components/modules/kelulusan/VerifikasiKelulusanModal.vue';
import AlumniFormModal from '../../components/modules/kelulusan/AlumniFormModal.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import { debounce } from '../../composables/useDebounce';
import type { Kelulusan, Siswa, TahunAjaran } from '../../types/models';
import type { PaginatedData } from '../../types/api';

interface Stats {
    totalLulus: number;
    totalAlumni: number;
    calonTingkat9: number;
}

const props = defineProps<{
    calonLulusan: Siswa[];
    kelulusanList: PaginatedData<Kelulusan>;
    tahunAjaranList: TahunAjaran[];
    tahunAktif: TahunAjaran | null;
    stats: Stats;
    filters: {
        search: string;
        kd_tahun: number | null;
    };
}>();

const { confirmAction } = useConfirmModal();

const search = ref(props.filters.search);
const selectedTahun = ref(
    props.filters.kd_tahun ? String(props.filters.kd_tahun) : '',
);

const isVerifikasiModalOpen = ref(false);
const isAlumniModalOpen = ref(false);
const selectedKelulusanForAlumni = ref<Kelulusan | null>(null);
const selectedSiswaForLulus = ref<Siswa | null>(null);

const applyFilter = debounce(() => {
    router.get(
        '/kelulusan',
        {
            search: search.value || undefined,
            kd_tahun: selectedTahun.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}, 300);

watch([search, selectedTahun], () => {
    applyFilter();
});

function handleOpenSahkan(siswa?: Siswa) {
    selectedSiswaForLulus.value = siswa || null;
    isVerifikasiModalOpen.value = true;
}

function handleOpenAlumni(item: Kelulusan) {
    selectedKelulusanForAlumni.value = item;
    isAlumniModalOpen.value = true;
}

function handleDeleteKelulusan(item: Kelulusan) {
    confirmAction({
        title: 'Batalkan Kelulusan Siswa',
        message: `Batalkan pengesahan kelulusan untuk "${item.siswa?.nm_siswa}"? Status siswa akan dikembalikan menjadi 'aktif', dan catatan alumni terkait akan otomatis dihapus.`,
        confirmText: 'Ya, Batalkan',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/kelulusan/${item.kd_lulus}`);
        },
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Pengesahan Kelulusan Siswa Tingkat 9" />

        <div class="space-y-6">
            <PageHeader
                title="Pengesahan Kelulusan Siswa Tingkat 9"
                description="Rekap evaluasi kelulusan akhir jenjang, pengesahan nomor seri ijazah, dan integrasi direktori alumni."
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Kelulusan & Alumni', href: '/kelulusan' },
                    { label: 'Pengesahan Kelulusan' },
                ]"
            >
                <template #actions>
                    <Button variant="primary" @click="handleOpenSahkan()">
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
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                        Sahkan Kelulusan
                    </Button>
                </template>
            </PageHeader>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-surface-500 text-xs font-medium tracking-wider uppercase"
                            >
                                Total Siswa Lulus
                            </p>
                            <p class="text-surface-900 mt-1 text-2xl font-bold">
                                {{ stats.totalLulus }}
                            </p>
                        </div>
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700"
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
                                    d="M12 14l9-5-9-5-9 5 9 5z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                                />
                            </svg>
                        </div>
                    </div>
                </Card>

                <Card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-surface-500 text-xs font-medium tracking-wider uppercase"
                            >
                                Tercatat di Alumni
                            </p>
                            <p class="text-surface-900 mt-1 text-2xl font-bold">
                                {{ stats.totalAlumni }}
                            </p>
                        </div>
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"
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
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </Card>

                <Card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-surface-500 text-xs font-medium tracking-wider uppercase"
                            >
                                Calon Lulusan Tingkat 9
                            </p>
                            <p class="text-surface-900 mt-1 text-2xl font-bold">
                                {{ stats.calonTingkat9 }}
                            </p>
                        </div>
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700"
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
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Calon Lulusan Pending Action Card -->
            <div
                v-if="calonLulusan.length > 0"
                class="rounded-xl border border-amber-200 bg-amber-50/70 p-4"
            >
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h3 class="text-sm font-semibold text-amber-900">
                            Terdapat {{ calonLulusan.length }} siswa aktif
                            tingkat 9 yang siap disahkan kelulusannya
                        </h3>
                        <p class="text-xs text-amber-700">
                            Pilih siswa dari daftar atau klik tombol pengesahan
                            untuk mulai memproses kelulusan.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="siswa in calonLulusan.slice(0, 3)"
                            :key="siswa.kd_siswa"
                            size="sm"
                            variant="secondary"
                            @click="handleOpenSahkan(siswa)"
                        >
                            + {{ siswa.nm_siswa }}
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <Card>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <Input
                        v-model="search"
                        placeholder="Cari nama siswa, NIS, atau nomor ijazah..."
                        clearable
                    />

                    <Select
                        v-model="selectedTahun"
                        :options="[
                            { label: 'Semua Tahun Ajaran', value: '' },
                            ...tahunAjaranList.map((t) => ({
                                label: `Tahun ${t.nm_tahun} (${t.semester})`,
                                value: String(t.kd_tahun),
                            })),
                        ]"
                    />
                </div>
            </Card>

            <!-- Table of Graduated Students -->
            <KelulusanTable
                :kelulusan-list="kelulusanList"
                @alumni="handleOpenAlumni"
                @delete="handleDeleteKelulusan"
            />
        </div>

        <!-- Modals -->
        <VerifikasiKelulusanModal
            :show="isVerifikasiModalOpen"
            :calon-lulusan="calonLulusan"
            :tahun-ajaran-list="tahunAjaranList"
            :tahun-aktif="tahunAktif"
            :selected-siswa="selectedSiswaForLulus"
            @close="isVerifikasiModalOpen = false"
        />

        <AlumniFormModal
            :show="isAlumniModalOpen"
            :kelulusan-item="selectedKelulusanForAlumni"
            @close="isAlumniModalOpen = false"
        />
    </AppLayout>
</template>
