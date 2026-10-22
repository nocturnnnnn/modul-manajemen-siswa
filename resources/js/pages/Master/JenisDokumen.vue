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
import type { JenisDokumen } from '../../types';

interface JenisDokumenWithCount extends JenisDokumen {
    dokumen_siswa_count?: number;
}

interface Props {
    jenisDokumenList: JenisDokumenWithCount[];
}

defineProps<Props>();

const { confirmAction } = useConfirmModal();

const headers: TableHeader[] = [
    { key: 'no', label: 'No.', width: 'w-16' },
    { key: 'kode', label: 'Kode', width: 'w-24' },
    { key: 'nm_jenis', label: 'Nama Dokumen' },
    { key: 'wajib', label: 'Sifat Dokumen' },
    { key: 'dokumen_count', label: 'Berkas Diunggah' },
    { key: 'actions', label: 'Aksi', align: 'right', width: 'w-32' },
];

const isModalOpen = ref(false);
const editingJenis = ref<JenisDokumenWithCount | null>(null);

const form = useForm({
    kode: '',
    nm_jenis: '',
    wajib: false,
});

function openCreateModal() {
    editingJenis.value = null;
    form.reset();
    form.clearErrors();
    form.wajib = false;
    isModalOpen.value = true;
}

function openEditModal(item: JenisDokumenWithCount) {
    editingJenis.value = item;
    form.kode = item.kode;
    form.nm_jenis = item.nm_jenis;
    form.wajib = item.wajib;
    form.clearErrors();
    isModalOpen.value = true;
}

function submitForm() {
    if (editingJenis.value) {
        form.put(`/master/jenis-dokumen/${editingJenis.value.kd_jenis}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/master/jenis-dokumen', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
}

function deleteJenis(item: JenisDokumenWithCount) {
    confirmAction({
        title: 'Hapus Jenis Dokumen',
        message: `Apakah Anda yakin ingin menghapus data jenis dokumen "${item.nm_jenis}" (${item.kode})?`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/master/jenis-dokumen/${item.kd_jenis}`);
        },
    });
}
</script>

<template>
    <AppLayout title="Master Jenis Dokumen">
        <Head title="Master Jenis Dokumen - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Master Jenis Dokumen Digital"
            subtitle="Klasifikasi berkas dokumen persyaratan siswa dan pendaftar baru"
            :breadcrumbs="[
                { label: 'Master Data' },
                { label: 'Jenis Dokumen', current: true },
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
                    Tambah Jenis Dokumen
                </Button>
            </template>
        </PageHeader>

        <DataTable :headers="headers" :items="jenisDokumenList">
            <template #default="{ items }">
                <tr
                    v-for="(item, index) in items as JenisDokumenWithCount[]"
                    :key="item.kd_jenis"
                    class="border-b border-border-subtle transition-colors hover:bg-primary-50/40"
                >
                    <td class="px-4 py-3.5 text-xs font-medium text-slate-500">
                        {{ index + 1 }}
                    </td>
                    <td
                        class="px-4 py-3.5 font-mono text-xs font-bold text-primary-700"
                    >
                        {{ item.kode }}
                    </td>
                    <td
                        class="px-4 py-3.5 text-sm font-semibold text-slate-900"
                    >
                        {{ item.nm_jenis }}
                    </td>
                    <td class="px-4 py-3.5 text-xs">
                        <Badge
                            :variant="item.wajib ? 'danger' : 'neutral'"
                            size="sm"
                        >
                            {{ item.wajib ? 'Wajib Dilampirkan' : 'Opsional' }}
                        </Badge>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-slate-600">
                        <Badge variant="neutral" size="sm">
                            {{ item.dokumen_siswa_count ?? 0 }} Berkas
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
                                @click="deleteJenis(item)"
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
                editingJenis
                    ? 'Edit Jenis Dokumen'
                    : 'Tambah Jenis Dokumen Baru'
            "
            size="sm"
            @close="isModalOpen = false"
        >
            <form
                id="jenisDokumenForm"
                class="space-y-4"
                @submit.prevent="submitForm"
            >
                <Input
                    v-model="form.kode"
                    label="Kode Singkat"
                    placeholder="Contoh: KK, AKTA, KIP, RAPOR"
                    required
                    :error="form.errors.kode"
                />

                <Input
                    v-model="form.nm_jenis"
                    label="Nama Jenis Dokumen"
                    placeholder="Contoh: Kartu Keluarga, Akta Kelahiran"
                    required
                    :error="form.errors.nm_jenis"
                />

                <div class="flex items-center gap-2 pt-1">
                    <input
                        id="wajibCheckbox"
                        v-model="form.wajib"
                        type="checkbox"
                        class="h-4 w-4 rounded border-border-subtle text-primary-600 focus:ring-primary-500"
                    />
                    <label
                        for="wajibCheckbox"
                        class="text-sm font-medium text-slate-700"
                    >
                        Wajib dilampirkan oleh setiap siswa
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
                    form="jenisDokumenForm"
                    :loading="form.processing"
                >
                    {{
                        editingJenis
                            ? 'Simpan Perubahan'
                            : 'Tambah Jenis Dokumen'
                    }}
                </Button>
            </template>
        </Modal>
    </AppLayout>
</template>
