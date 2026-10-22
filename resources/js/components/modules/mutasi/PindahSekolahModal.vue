<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../../ui/Modal.vue';
import Button from '../../ui/Button.vue';
import Input from '../../ui/Input.vue';
import Select from '../../ui/Select.vue';
import Textarea from '../../ui/Textarea.vue';

interface SiswaOption {
    kd_siswa: number;
    nis: string;
    nm_siswa: string;
}

const props = defineProps<{
    show: boolean;
    siswaList: SiswaOption[];
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm<{
    kd_siswa: number | '';
    sekolah_tujuan: string;
    tgl_pindah: string;
    alasan: string;
}>({
    kd_siswa: '',
    sekolah_tujuan: '',
    tgl_pindah: new Date().toISOString().slice(0, 10),
    alasan: '',
});

watch(
    () => props.show,
    (open) => {
        if (open) {
            form.clearErrors();
            form.kd_siswa =
                props.siswaList.length > 0 ? props.siswaList[0].kd_siswa : '';
            form.sekolah_tujuan = '';
            form.tgl_pindah = new Date().toISOString().slice(0, 10);
            form.alasan = '';
        }
    },
);

function handleSubmit() {
    form.post('/mutasi/pindah', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}
</script>

<template>
    <Modal
        :show="show"
        title="Catat Siswa Pindah Sekolah Keluar"
        size="md"
        @close="emit('close')"
    >
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <Select
                v-model="form.kd_siswa"
                label="Pilih Siswa"
                required
                :options="
                    siswaList.map((s) => ({
                        label: `${s.nm_siswa} (NIS: ${s.nis})`,
                        value: s.kd_siswa,
                    }))
                "
                placeholder="-- Pilih Siswa --"
                :error="form.errors.kd_siswa"
            />

            <Input
                v-model="form.sekolah_tujuan"
                label="Nama Sekolah Tujuan"
                placeholder="Contoh: SMP Negeri 1 Wonosobo / SMP Luar Kota"
                required
                :error="form.errors.sekolah_tujuan"
            />

            <Input
                v-model="form.tgl_pindah"
                type="date"
                label="Tanggal Efektif Pindah"
                required
                :error="form.errors.tgl_pindah"
            />

            <Textarea
                v-model="form.alasan"
                label="Alasan Kepindahan Sekolah"
                placeholder="Jelaskan alasan siswa pindah (misal: domisili orang tua, minat bakat)..."
                required
                :rows="3"
                :error="form.errors.alasan"
            />

            <div
                class="flex items-center justify-end gap-2 border-t border-border-subtle pt-3"
            >
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="emit('close')"
                >
                    Batal
                </Button>
                <Button
                    type="submit"
                    variant="primary"
                    size="sm"
                    :loading="form.processing"
                >
                    Simpan Data Pindah
                </Button>
            </div>
        </form>
    </Modal>
</template>
