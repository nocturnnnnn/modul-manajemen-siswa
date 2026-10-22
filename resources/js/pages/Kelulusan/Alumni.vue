<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Card from '../../components/ui/Card.vue';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Select from '../../components/ui/Select.vue';
import Pagination from '../../components/ui/Pagination.vue';
import AlumniDetailCard from '../../components/modules/kelulusan/AlumniDetailCard.vue';
import AlumniFormModal from '../../components/modules/kelulusan/AlumniFormModal.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import { debounce } from '../../composables/useDebounce';
import type { Alumni, Kelulusan } from '../../types/models';
import type { PaginatedData } from '../../types/api';

interface Stats {
    totalAlumni: number;
    melanjutkanSMA: number;
    melanjutkanSMK: number;
    belumTercatat: number;
}

const props = defineProps<{
    alumniList: PaginatedData<Alumni>;
    lulusanBelumAlumni: Kelulusan[];
    stats: Stats;
    filters: {
        search: string;
        kegiatan: string;
    };
}>();

const { confirmAction } = useConfirmModal();

const search = ref(props.filters.search);
const selectedKegiatan = ref(props.filters.kegiatan);

const isModalOpen = ref(false);
const selectedAlumni = ref<Alumni | null>(null);
const selectedKelulusan = ref<Kelulusan | null>(null);

const kegiatanOptions = [
    { label: 'Semua Kegiatan / Jalur', value: '' },
    { label: 'Melanjutkan SMA', value: 'Melanjutkan SMA' },
    { label: 'Melanjutkan SMK', value: 'Melanjutkan SMK' },
    {
        label: 'Melanjutkan MA / Pesantren',
        value: 'Melanjutkan MA / Pesantren',
    },
    { label: 'Bekerja / Wirausaha', value: 'Bekerja / Wirausaha' },
    { label: 'Lainnya', value: 'Lainnya' },
];

const applyFilter = debounce(() => {
    router.get(
        '/alumni',
        {
            search: search.value || undefined,
            kegiatan: selectedKegiatan.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}, 300);

watch([search, selectedKegiatan], () => {
    applyFilter();
});

function handleOpenCreate(kelulusan?: Kelulusan) {
    selectedAlumni.value = null;
    selectedKelulusan.value = kelulusan || null;
    isModalOpen.value = true;
}

function handleOpenEdit(alumni: Alumni) {
    selectedAlumni.value = alumni;
    selectedKelulusan.value = alumni.kelulusan || null;
    isModalOpen.value = true;
}

function handleDeleteAlumni(alumni: Alumni) {
    confirmAction({
        title: 'Hapus Data Alumni',
        message: `Hapus catatan direktori alumni "${alumni.kelulusan?.siswa?.nm_siswa}"? Status siswa akan dikembalikan menjadi 'lulus'.`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/alumni/${alumni.kd_alumni}`);
        },
    });
}
</script>

<template>
    <AppLayout>
        <Head title="Direktori & Pelacakan Alumni" />

        <div class="space-y-6">
            <PageHeader
                title="Direktori & Pelacakan Alumni"
                description="Pelacakan studi lanjut ke jenjang SMA/SMK/MA atau dunia kerja bagi lulusan SMP Kristen 1 Wonosobo."
                :breadcrumbs="[
                    { label: 'Dashboard', href: '/' },
                    { label: 'Kelulusan & Alumni', href: '/kelulusan' },
                    { label: 'Direktori Alumni' },
                ]"
            >
                <template #actions>
                    <Button variant="primary" @click="handleOpenCreate()">
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
                        Catat Alumni Baru
                    </Button>
                </template>
            </PageHeader>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <Card>
                    <p
                        class="text-surface-500 text-xs font-medium tracking-wider uppercase"
                    >
                        Total Alumni Terdata
                    </p>
                    <p class="text-surface-900 mt-1 text-2xl font-bold">
                        {{ stats.totalAlumni }}
                    </p>
                </Card>

                <Card>
                    <p
                        class="text-surface-500 text-xs font-medium tracking-wider uppercase"
                    >
                        Melanjutkan SMA
                    </p>
                    <p class="mt-1 text-2xl font-bold text-primary-600">
                        {{ stats.melanjutkanSMA }}
                    </p>
                </Card>

                <Card>
                    <p
                        class="text-surface-500 text-xs font-medium tracking-wider uppercase"
                    >
                        Melanjutkan SMK
                    </p>
                    <p class="mt-1 text-2xl font-bold text-sky-600">
                        {{ stats.melanjutkanSMK }}
                    </p>
                </Card>

                <Card>
                    <p
                        class="text-surface-500 text-xs font-medium tracking-wider uppercase"
                    >
                        Lulusan Belum Tercatat
                    </p>
                    <p class="mt-1 text-2xl font-bold text-amber-600">
                        {{ stats.belumTercatat }}
                    </p>
                </Card>
            </div>

            <!-- Callout for graduates not yet tracked -->
            <div
                v-if="lulusanBelumAlumni.length > 0"
                class="rounded-xl border border-sky-200 bg-sky-50/70 p-4"
            >
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h3 class="text-sm font-semibold text-sky-950">
                            Terdapat {{ lulusanBelumAlumni.length }} lulusan
                            yang belum memiliki data pelacakan alumni
                        </h3>
                        <p class="text-xs text-sky-800">
                            Pilih siswa lulusan di bawah ini untuk mencatat
                            sekolah lanjutan atau instansi tujuan mereka.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            v-for="item in lulusanBelumAlumni.slice(0, 3)"
                            :key="item.kd_lulus"
                            size="sm"
                            variant="secondary"
                            @click="handleOpenCreate(item)"
                        >
                            + {{ item.siswa?.nm_siswa }}
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <Card>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <Input
                        v-model="search"
                        placeholder="Cari nama alumni, instansi, atau NIS..."
                        clearable
                    />

                    <Select
                        v-model="selectedKegiatan"
                        :options="kegiatanOptions"
                    />
                </div>
            </Card>

            <!-- Alumni Grid -->
            <div
                v-if="alumniList.data.length > 0"
                class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"
            >
                <AlumniDetailCard
                    v-for="alumni in alumniList.data"
                    :key="alumni.kd_alumni"
                    :alumni="alumni"
                    @edit="handleOpenEdit"
                    @delete="handleDeleteAlumni"
                />
            </div>

            <div
                v-else
                class="border-surface-200 text-surface-400 rounded-xl border bg-white p-12 text-center"
            >
                <div class="flex flex-col items-center justify-center gap-2">
                    <svg
                        class="text-surface-300 h-12 w-12"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                    <p class="text-sm font-medium">
                        Belum ada data pelacakan alumni yang sesuai kriteria
                        pencarian
                    </p>
                </div>
            </div>

            <!-- Pagination -->
            <div
                v-if="alumniList.links && alumniList.links.length > 3"
                class="flex justify-center pt-2"
            >
                <Pagination
                    :links="alumniList.links"
                    :from="alumniList.from"
                    :to="alumniList.to"
                    :total="alumniList.total"
                />
            </div>
        </div>

        <!-- Alumni Form Modal -->
        <AlumniFormModal
            :show="isModalOpen"
            :kelulusan-item="selectedKelulusan"
            :lulusan-options="lulusanBelumAlumni"
            @close="isModalOpen = false"
        />
    </AppLayout>
</template>
