<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../../ui/Modal.vue';
import Button from '../../ui/Button.vue';
import Input from '../../ui/Input.vue';
import Select from '../../ui/Select.vue';

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
    jenis_mutasi: 'Masuk' | 'Keluar' | 'Internal';
    tgl_mutasi: string;
    no_surat: string;
}>({
    kd_siswa: '',
    jenis_mutasi: 'Keluar',
    tgl_mutasi: new Date().toISOString().slice(0, 10),
    no_surat: '',
});

watch(
    () => props.show,
    (open) => {
        if (open) {
            form.clearErrors();
            form.kd_siswa =
                props.siswaList.length > 0 ? props.siswaList[0].kd_siswa : '';
            form.jenis_mutasi = 'Keluar';
            form.tgl_mutasi = new Date().toISOString().slice(0, 10);
            form.no_surat = '';
        }
    },
);

function handleSubmit() {
    form.post('/mutasi/catat', {
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
        title="Catat Surat Mutasi Siswa"
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

            <Select
                v-model="form.jenis_mutasi"
                label="Jenis Mutasi"
                required
                :options="[
                    {
                        label: 'Mutasi Keluar (Pindah ke luar sekolah)',
                        value: 'Keluar',
                    },
                    {
                        label: 'Mutasi Masuk (Siswa pindahan masuk)',
                        value: 'Masuk',
                    },
                    {
                        label: 'Mutasi Internal (Perubahan status internal)',
                        value: 'Internal',
                    },
                ]"
                :error="form.errors.jenis_mutasi"
            />

            <Input
                v-model="form.tgl_mutasi"
                type="date"
                label="Tanggal Mutasi Resmi"
                required
                :error="form.errors.tgl_mutasi"
            />

            <Input
                v-model="form.no_surat"
                label="Nomor Surat Resmi"
                placeholder="Contoh: 421.3/085/SMPK.1/2026"
                required
                :error="form.errors.no_surat"
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
                    Simpan Surat Mutasi
                </Button>
            </div>
        </form>
    </Modal>
</template>
