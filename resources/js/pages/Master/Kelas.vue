<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import DataTable, { type TableHeader } from '../../components/ui/DataTable.vue';
import Modal from '../../components/ui/Modal.vue';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Select from '../../components/ui/Select.vue';
import Badge from '../../components/ui/Badge.vue';
import Card from '../../components/ui/Card.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import type { Kelas, SelectOption } from '../../types';

interface KelasWithCount extends Kelas {
    kenaikan_kelas_asal_count?: number;
    kenaikan_kelas_tujuan_count?: number;
}

interface Props {
    kelasList: KelasWithCount[];
    filters?: {
        search?: string;
        tingkat?: string | number;
    };
}

const props = defineProps<Props>();

const { confirmAction } = useConfirmModal();

const headers: TableHeader[] = [
    { key: 'no', label: 'No.', width: 'w-16' },
    { key: 'nm_kelas', label: 'Nama Rombel' },
    { key: 'tingkat', label: 'Tingkat Jenjang' },
    { key: 'riwayat', label: 'Aktivitas Akademik' },
    { key: 'actions', label: 'Aksi', align: 'right', width: 'w-32' },
];

const tingkatOptions: SelectOption<number>[] = [
    { label: 'Tingkat 7 (Kelas VII)', value: 7 },
    { label: 'Tingkat 8 (Kelas VIII)', value: 8 },
    { label: 'Tingkat 9 (Kelas IX)', value: 9 },
];

const isModalOpen = ref(false);
const editingKelas = ref<KelasWithCount | null>(null);

const form = useForm({
    nm_kelas: '',
    tingkat: 7 as number,
});

function openCreateModal() {
    editingKelas.value = null;
    form.reset();
    form.clearErrors();
    form.tingkat = 7;
    isModalOpen.value = true;
}

function openEditModal(item: KelasWithCount) {
    editingKelas.value = item;
    form.nm_kelas = item.nm_kelas;
    form.tingkat = item.tingkat;
    form.clearErrors();
    isModalOpen.value = true;
}

function submitForm() {
    if (editingKelas.value) {
        form.put(`/master/kelas/${editingKelas.value.kd_kelas}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/master/kelas', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
}

function deleteKelas(item: KelasWithCount) {
    confirmAction({
        title: 'Hapus Rombel Kelas',
        message: `Apakah Anda yakin ingin menghapus kelas "${item.nm_kelas}" (Tingkat ${item.tingkat})?`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/master/kelas/${item.kd_kelas}`);
        },
    });
}

function filterByTingkat(tingkatValue: number | null) {
    router.get(
        '/master/kelas',
        {
            search: props.filters?.search || undefined,
            tingkat: tingkatValue || undefined,
        },
        { preserveState: true, preserveScroll: true },
    );
}
</script>

<template>
    <AppLayout title="Master Rombel Kelas">
        <Head title="Master Kelas & Rombel - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Master Kelas & Rombongan Belajar"
            subtitle="Manajemen pembagian rombel belajar tingkat 7, 8, dan 9 SMP Kristen 1 Wonosobo"
            :breadcrumbs="[
                { label: 'Master Data' },
                { label: 'Kelas & Rombel', current: true },
            ]"
        >
            <template #actions>
                <Button variant="primary" size="sm" @click="openCreateModal">
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
                    Tambah Kelas
                </Button>
            </template>
        </PageHeader>

        <!-- Quick Filters -->
        <Card padding="sm" class="mb-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span
                        class="text-xs font-semibold tracking-wider text-slate-500 uppercase"
                    >
                        Filter Tingkat:
                    </span>
                    <Button
                        :variant="!filters?.tingkat ? 'primary' : 'outline'"
                        size="sm"
                        @click="filterByTingkat(null)"
                    >
                        Semua
                    </Button>
                    <Button
                        :variant="
                            Number(filters?.tingkat) === 7
                                ? 'primary'
                                : 'outline'
                        "
                        size="sm"
                        @click="filterByTingkat(7)"
                    >
                        Tingkat 7
                    </Button>
                    <Button
                        :variant="
                            Number(filters?.tingkat) === 8
                                ? 'primary'
                                : 'outline'
                        "
                        size="sm"
                        @click="filterByTingkat(8)"
                    >
                        Tingkat 8
                    </Button>
                    <Button
                        :variant="
                            Number(filters?.tingkat) === 9
                                ? 'primary'
                                : 'outline'
                        "
                        size="sm"
                        @click="filterByTingkat(9)"
                    >
                        Tingkat 9
                    </Button>
                </div>

                <div class="text-xs text-slate-500">
                    Total:
                    <strong class="text-slate-800">{{
                        kelasList.length
                    }}</strong>
                    Rombel Terdata
                </div>
            </div>
        </Card>

        <DataTable :headers="headers" :items="kelasList">
            <template #default="{ items }">
                <tr
                    v-for="(item, index) in items as KelasWithCount[]"
                    :key="item.kd_kelas"
                    class="border-b border-border-subtle transition-colors hover:bg-primary-50/40"
                >
                    <td class="px-4 py-3.5 text-xs font-medium text-slate-500">
                        {{ index + 1 }}
                    </td>
                    <td class="px-4 py-3.5 text-sm font-bold text-primary-950">
                        {{ item.nm_kelas }}
                    </td>
                    <td class="px-4 py-3.5 text-xs">
                        <Badge
                            :variant="
                                item.tingkat === 7
                                    ? 'primary'
                                    : item.tingkat === 8
                                      ? 'secondary'
                                      : 'info'
                            "
                            size="sm"
                        >
                            Tingkat {{ item.tingkat }}
                        </Badge>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-slate-600">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="text-slate-500">Asal:</span>
                            <strong>{{
                                item.kenaikan_kelas_asal_count ?? 0
                            }}</strong>
                            <span class="text-slate-300">•</span>
                            <span class="text-slate-500">Tujuan:</span>
                            <strong>{{
                                item.kenaikan_kelas_tujuan_count ?? 0
                            }}</strong>
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <Button
                                variant="outline"
                                size="sm"
                                @click="openEditModal(item)"
                            >
                                Edit
                            </Button>
                            <Button
                                variant="danger"
                                size="sm"
                                @click="deleteKelas(item)"
                            >
                                Hapus
                            </Button>
                        </div>
                    </td>
                </tr>
            </template>
        </DataTable>

        <!-- Modal Form Create / Edit -->
        <Modal
            :show="isModalOpen"
            :title="editingKelas ? 'Edit Rombel Kelas' : 'Tambah Rombel Baru'"
            size="sm"
            @close="isModalOpen = false"
        >
            <form id="kelasForm" class="space-y-4" @submit.prevent="submitForm">
                <Input
                    v-model="form.nm_kelas"
                    label="Nama Rombel"
                    placeholder="Contoh: 7A, 8B, 9C"
                    required
                    :error="form.errors.nm_kelas"
                />

                <Select
                    :model-value="form.tingkat"
                    label="Tingkat Jenjang"
                    :options="tingkatOptions"
                    required
                    :error="form.errors.tingkat"
                    @update:model-value="(val) => (form.tingkat = Number(val))"
                />
            </form>

            <template #footer>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="form.processing"
                    @click="isModalOpen = false"
                >
                    Batal
                </Button>
                <Button
                    variant="primary"
                    size="sm"
                    type="submit"
                    form="kelasForm"
                    :loading="form.processing"
                >
                    {{ editingKelas ? 'Simpan Perubahan' : 'Tambah Kelas' }}
                </Button>
            </template>
        </Modal>
    </AppLayout>
</template>
