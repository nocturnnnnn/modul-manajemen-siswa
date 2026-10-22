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
import { useConfirmModal } from '../../composables/useConfirmModal';
import type { TahunAjaran, SelectOption } from '../../types';

interface TahunAjaranWithCount extends TahunAjaran {
    kenaikan_kelas_count?: number;
    kelulusan_count?: number;
}

interface Props {
    tahunAjaranList: TahunAjaranWithCount[];
}

defineProps<Props>();

const { confirmAction } = useConfirmModal();

const headers: TableHeader[] = [
    { key: 'no', label: 'No.', width: 'w-16' },
    { key: 'nm_tahun', label: 'Tahun Ajaran' },
    { key: 'semester', label: 'Semester' },
    { key: 'aktif', label: 'Status Berjalan' },
    { key: 'actions', label: 'Aksi', align: 'right', width: 'w-44' },
];

const semesterOptions: SelectOption<string>[] = [
    { label: 'Semester Ganjil', value: 'Ganjil' },
    { label: 'Semester Genap', value: 'Genap' },
];

const isModalOpen = ref(false);
const editingTahun = ref<TahunAjaranWithCount | null>(null);

const form = useForm({
    nm_tahun: '',
    semester: 'Ganjil' as 'Ganjil' | 'Genap',
    aktif: false,
});

function openCreateModal() {
    editingTahun.value = null;
    form.reset();
    form.clearErrors();
    form.semester = 'Ganjil';
    form.aktif = false;
    isModalOpen.value = true;
}

function openEditModal(item: TahunAjaranWithCount) {
    editingTahun.value = item;
    form.nm_tahun = item.nm_tahun;
    form.semester = item.semester;
    form.aktif = item.aktif;
    form.clearErrors();
    isModalOpen.value = true;
}

function submitForm() {
    if (editingTahun.value) {
        form.put(`/master/tahun-ajaran/${editingTahun.value.kd_tahun}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/master/tahun-ajaran', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
}

function activateTahunAjaran(item: TahunAjaranWithCount) {
    confirmAction({
        title: 'Aktifkan Tahun Ajaran',
        message: `Aktifkan tahun ajaran ${item.nm_tahun} (${item.semester}) sebagai tahun ajaran operasional saat ini? Tahun ajaran lain akan dinonaktifkan secara otomatis.`,
        confirmText: 'Ya, Aktifkan',
        isDanger: false,
        onConfirm: () => {
            router.post(`/master/tahun-ajaran/${item.kd_tahun}/activate`);
        },
    });
}

function deleteTahunAjaran(item: TahunAjaranWithCount) {
    confirmAction({
        title: 'Hapus Tahun Ajaran',
        message: `Apakah Anda yakin ingin menghapus data tahun ajaran ${item.nm_tahun} (${item.semester})?`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/master/tahun-ajaran/${item.kd_tahun}`);
        },
    });
}
</script>

<template>
    <AppLayout title="Master Tahun Ajaran">
        <Head title="Master Tahun Ajaran - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Master Tahun Ajaran & Semester"
            subtitle="Pengaturan kalender akademik dan tahun ajaran aktif SMP Kristen 1 Wonosobo"
            :breadcrumbs="[
                { label: 'Master Data' },
                { label: 'Tahun Ajaran', current: true },
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
                    Tambah Tahun Ajaran
                </Button>
            </template>
        </PageHeader>

        <DataTable :headers="headers" :items="tahunAjaranList">
            <template #default="{ items }">
                <tr
                    v-for="(item, index) in items as TahunAjaranWithCount[]"
                    :key="item.kd_tahun"
                    class="border-b border-border-subtle transition-colors hover:bg-primary-50/40"
                >
                    <td class="px-4 py-3.5 text-xs font-medium text-slate-500">
                        {{ index + 1 }}
                    </td>
                    <td class="px-4 py-3.5 text-sm font-bold text-slate-900">
                        {{ item.nm_tahun }}
                    </td>
                    <td class="px-4 py-3.5 text-xs text-slate-700">
                        <span
                            class="inline-flex items-center gap-1.5 font-medium"
                        >
                            {{ item.semester }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-xs">
                        <Badge
                            v-if="item.aktif"
                            variant="success"
                            size="sm"
                            dot
                        >
                            Sedang Berjalan (Aktif)
                        </Badge>
                        <Badge v-else variant="neutral" size="sm">
                            Tidak Aktif
                        </Badge>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <Button
                                v-if="!item.aktif"
                                variant="secondary"
                                size="sm"
                                @click="activateTahunAjaran(item)"
                            >
                                Aktifkan
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                @click="openEditModal(item)"
                            >
                                Edit
                            </Button>
                            <Button
                                v-if="!item.aktif"
                                variant="danger"
                                size="sm"
                                @click="deleteTahunAjaran(item)"
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
            :title="
                editingTahun ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru'
            "
            size="sm"
            @close="isModalOpen = false"
        >
            <form
                id="tahunAjaranForm"
                class="space-y-4"
                @submit.prevent="submitForm"
            >
                <Input
                    v-model="form.nm_tahun"
                    label="Tahun Ajaran"
                    placeholder="Contoh: 2026/2027"
                    hint="Format: TTTT/TTTT"
                    required
                    :error="form.errors.nm_tahun"
                />

                <Select
                    :model-value="form.semester"
                    label="Semester"
                    :options="semesterOptions"
                    required
                    :error="form.errors.semester"
                    @update:model-value="
                        (val) => (form.semester = val as 'Ganjil' | 'Genap')
                    "
                />

                <div class="flex items-center gap-2 pt-1">
                    <input
                        id="aktifCheckbox"
                        v-model="form.aktif"
                        type="checkbox"
                        class="h-4 w-4 rounded border-border-subtle text-primary-600 focus:ring-primary-500"
                    />
                    <label
                        for="aktifCheckbox"
                        class="text-sm font-medium text-slate-700"
                    >
                        Jadikan tahun ajaran aktif berjalan
                    </label>
                </div>
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
                    form="tahunAjaranForm"
                    :loading="form.processing"
                >
                    {{
                        editingTahun
                            ? 'Simpan Perubahan'
                            : 'Tambah Tahun Ajaran'
                    }}
                </Button>
            </template>
        </Modal>
    </AppLayout>
</template>
