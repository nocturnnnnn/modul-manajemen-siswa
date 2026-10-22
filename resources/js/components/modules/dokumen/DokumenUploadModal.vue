<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../../ui/Modal.vue';
import Button from '../../ui/Button.vue';
import Input from '../../ui/Input.vue';
import Select from '../../ui/Select.vue';
import FileUpload from '../../ui/FileUpload.vue';
import type { JenisDokumen } from '../../../types';

interface SiswaOption {
    kd_siswa: number;
    nis: string;
    nm_siswa: string;
}

const props = defineProps<{
    isOpen: boolean;
    preselectedSiswaId?: number;
    siswaList?: SiswaOption[];
    jenisDokumenList: JenisDokumen[];
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm<{
    kd_siswa: number | '';
    kd_jenis: number | '';
    nomor_dokumen: string;
    file: File | null;
}>({
    kd_siswa: '',
    kd_jenis: '',
    nomor_dokumen: '',
    file: null,
});

watch(
    () => props.isOpen,
    (open) => {
        if (open) {
            form.clearErrors();
            form.kd_siswa = props.preselectedSiswaId ?? '';
            form.kd_jenis =
                props.jenisDokumenList.length > 0
                    ? props.jenisDokumenList[0].kd_jenis
                    : '';
            form.nomor_dokumen = '';
            form.file = null;
        }
    },
);

function handleSubmit() {
    form.post('/dokumen/upload', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}
</script>

<template>
    <Modal
        :show="isOpen"
        title="Unggah Dokumen Digital Siswa"
        size="md"
        @close="$emit('close')"
    >
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Siswa Selector (if not preselected) -->
            <div v-if="!preselectedSiswaId">
                <Select
                    v-model="form.kd_siswa"
                    label="Pilih Siswa"
                    required
                    :options="
                        (siswaList || []).map((s) => ({
                            label: `${s.nm_siswa} (NIS: ${s.nis})`,
                            value: s.kd_siswa,
                        }))
                    "
                    placeholder="-- Pilih Siswa --"
                    :error="form.errors.kd_siswa"
                />
            </div>

            <!-- Jenis Dokumen Selector -->
            <div>
                <Select
                    v-model="form.kd_jenis"
                    label="Jenis Dokumen"
                    required
                    :options="
                        jenisDokumenList.map((j) => ({
                            label: `${j.nm_jenis} (${j.kode})${j.wajib ? ' - Wajib' : ''}`,
                            value: j.kd_jenis,
                        }))
                    "
                    placeholder="-- Pilih Jenis Dokumen --"
                    :error="form.errors.kd_jenis"
                />
            </div>

            <!-- Nomor Dokumen (Opsional) -->
            <div>
                <Input
                    v-model="form.nomor_dokumen"
                    label="Nomor Dokumen (Opsional)"
                    placeholder="Contoh: 330701..., No. Akta, dll"
                    :error="form.errors.nomor_dokumen"
                />
            </div>

            <!-- File Upload -->
            <div>
                <FileUpload
                    v-model="form.file"
                    label="Berkas Dokumen"
                    required
                    accept=".pdf,.jpg,.jpeg,.png"
                    :max-size-mb="3"
                    helper-text="PDF, JPG, JPEG, atau PNG (Maks 3MB)"
                    :error="form.errors.file"
                />
            </div>

            <!-- Actions Footer -->
            <div
                class="flex items-center justify-end gap-2 border-t border-border-subtle pt-3"
            >
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="$emit('close')"
                >
                    Batal
                </Button>
                <Button
                    type="submit"
                    variant="primary"
                    size="sm"
                    :loading="form.processing"
                >
                    Unggah Dokumen
                </Button>
            </div>
        </form>
    </Modal>
</template>
