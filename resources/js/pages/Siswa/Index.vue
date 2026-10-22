<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Button from '../../components/ui/Button.vue';
import SiswaFilterBar from '../../components/modules/siswa/SiswaFilterBar.vue';
import SiswaTable from '../../components/modules/siswa/SiswaTable.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import type { Agama, PaginatedData, Siswa } from '../../types';

interface Filters {
    search?: string;
    status?: string;
    agama?: string | number;
}

const props = defineProps<{
    siswaList: PaginatedData<Siswa>;
    agamaList: Agama[];
    filters: Filters;
}>();

const { confirmAction } = useConfirmModal();

function handleFilter(newFilters: Filters) {
    router.get('/siswa', newFilters as Record<string, string>, {
        preserveState: true,
        preserveScroll: true,
    });
}

function handleReset() {
    router.get('/siswa', {}, { preserveState: false });
}

function handleDelete(siswa: Siswa) {
    confirmAction({
        title: 'Hapus Data Siswa',
        message: `Apakah Anda yakin ingin menghapus data siswa "${siswa.nm_siswa}" (NIS: ${siswa.nis})? Seluruh data keluarga, kontak, dan riwayat yang terkait akan terhapus.`,
        confirmText: 'Ya, Hapus Data',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/siswa/${siswa.kd_siswa}`);
        },
    });
}
</script>

<template>
    <AppLayout title="Data Siswa">
        <Head title="Data Siswa - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Direktori Data Siswa"
            subtitle="Manajemen data pokok siswa, identitas kependudukan, kontak keluarga, dan status akademik"
            :breadcrumbs="[{ label: 'Data Siswa', current: true }]"
        >
            <template #actions>
                <Link href="/siswa/create">
                    <Button variant="primary" size="sm">
                        <svg
                            class="h-4 w-4"
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
                        Tambah Siswa Baru
                    </Button>
                </Link>
            </template>
        </PageHeader>

        <SiswaFilterBar
            :filters="filters"
            :agama-list="agamaList"
            @filter="handleFilter"
            @reset="handleReset"
        />

        <SiswaTable :siswa-list="siswaList" @delete="handleDelete" />
    </AppLayout>
</template>
