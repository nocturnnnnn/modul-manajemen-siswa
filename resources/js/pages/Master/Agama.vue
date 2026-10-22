<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import DataTable, { type TableHeader } from '../../components/ui/DataTable.vue';
import Modal from '../../components/ui/Modal.vue';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Badge from '../../components/ui/Badge.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import type { Agama } from '../../types';

interface AgamaWithCount extends Agama {
    siswa_count?: number;
}

interface Props {
    agamaList: AgamaWithCount[];
}

defineProps<Props>();

const { confirmAction } = useConfirmModal();

const headers: TableHeader[] = [
    { key: 'no', label: 'No.', width: 'w-16' },
    { key: 'nm_agama', label: 'Nama Agama' },
    { key: 'siswa_count', label: 'Jumlah Siswa' },
    { key: 'actions', label: 'Aksi', align: 'right', width: 'w-32' },
];

const isModalOpen = ref(false);
const editingAgama = ref<AgamaWithCount | null>(null);

const form = useForm({
    nm_agama: '',
});

function openCreateModal() {
    editingAgama.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
}

function openEditModal(item: AgamaWithCount) {
    editingAgama.value = item;
    form.nm_agama = item.nm_agama;
    form.clearErrors();
    isModalOpen.value = true;
}

function submitForm() {
    if (editingAgama.value) {
        form.put(`/master/agama/${editingAgama.value.kd_agama}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/master/agama', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
}

function deleteAgama(item: AgamaWithCount) {
    confirmAction({
        title: 'Hapus Referensi Agama',
        message: `Apakah Anda yakin ingin menghapus data agama "${item.nm_agama}"?`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/master/agama/${item.kd_agama}`);
        },
    });
}
</script>

<template>
    <AppLayout title="Master Data Agama">
        <Head title="Master Data Agama - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Master Data Agama"
            subtitle="Daftar referensi agama resmi bagi siswa SMP Kristen 1 Wonosobo"
            :breadcrumbs="[
                { label: 'Master Data' },
                { label: 'Agama', current: true },
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
                    Tambah Agama
                </Button>
            </template>
        </PageHeader>

        <DataTable :headers="headers" :items="agamaList">
            <template #default="{ items }">
                <tr
                    v-for="(item, index) in items as AgamaWithCount[]"
                    :key="item.kd_agama"
                    class="border-b border-border-subtle transition-colors hover:bg-primary-50/40"
                >
                    <td class="px-4 py-3.5 text-xs font-medium text-slate-500">
                        {{ index + 1 }}
                    </td>
                    <td
                        class="px-4 py-3.5 text-sm font-semibold text-slate-800"
                    >
                        {{ item.nm_agama }}
                    </td>
                    <td class="px-4 py-3.5 text-xs text-slate-600">
                        <Badge variant="neutral" size="sm">
                            {{ item.siswa_count ?? 0 }} Siswa
                        </Badge>
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
                                @click="deleteAgama(item)"
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
            :title="editingAgama ? 'Edit Data Agama' : 'Tambah Data Agama'"
            size="sm"
            @close="isModalOpen = false"
        >
            <form id="agamaForm" class="space-y-4" @submit.prevent="submitForm">
                <Input
                    v-model="form.nm_agama"
                    label="Nama Agama"
                    placeholder="Contoh: Kristen, Katolik, dll."
                    required
                    :error="form.errors.nm_agama"
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
                    form="agamaForm"
                    :loading="form.processing"
                >
                    {{ editingAgama ? 'Simpan Perubahan' : 'Tambah Agama' }}
                </Button>
            </template>
        </Modal>
    </AppLayout>
</template>
