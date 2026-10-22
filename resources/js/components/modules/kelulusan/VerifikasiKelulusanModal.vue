<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../../ui/Modal.vue';
import Button from '../../ui/Button.vue';
import Input from '../../ui/Input.vue';
import Select from '../../ui/Select.vue';
import type { Siswa, TahunAjaran } from '../../../types/models';

const props = defineProps<{
    show: boolean;
    calonLulusan: Siswa[];
    tahunAjaranList: TahunAjaran[];
    tahunAktif: TahunAjaran | null;
    selectedSiswa?: Siswa | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm<{
    kd_siswa: number | '';
    kd_tahun: number | '';
    tgl_lulus: string;
    no_ijazah: string;
}>({
    kd_siswa: '',
    kd_tahun: '',
    tgl_lulus: new Date().toISOString().slice(0, 10),
    no_ijazah: '',
});

watch(
    () => props.show,
    (open) => {
        if (open) {
            form.clearErrors();
            form.kd_siswa =
                props.selectedSiswa?.kd_siswa ||
                (props.calonLulusan.length > 0
                    ? props.calonLulusan[0].kd_siswa
                    : '');
            form.kd_tahun =
                props.tahunAktif?.kd_tahun ||
                (props.tahunAjaranList.length > 0
                    ? props.tahunAjaranList[0].kd_tahun
                    : '');
            form.tgl_lulus = new Date().toISOString().slice(0, 10);
            form.no_ijazah = '';
        }
    },
);

function handleSubmit() {
    form.post('/kelulusan/proses', {
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
        title="Pengesahan Kelulusan Siswa Tingkat 9"
        size="md"
        @close="emit('close')"
    >
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="rounded-lg bg-primary-50 p-3 text-xs text-primary-800">
                <span class="font-semibold">Informasi:</span> Pengesahan
                kelulusan hanya berlaku untuk siswa aktif tingkat 9 yang telah
                menuntaskan seluruh proses evaluasi akademik.
            </div>

            <Select
                v-model="form.kd_siswa"
                label="Pilih Calon Lulusan (Tingkat 9)"
                required
                :options="
                    calonLulusan.map((s) => ({
                        label: `${s.nm_siswa} (NIS: ${s.nis})`,
                        value: s.kd_siswa,
                    }))
                "
                placeholder="-- Pilih Calon Siswa Lulus --"
                :error="form.errors.kd_siswa"
            />

            <Select
                v-model="form.kd_tahun"
                label="Tahun Ajaran Kelulusan"
                required
                :options="
                    tahunAjaranList.map((t) => ({
                        label: `${t.nm_tahun} (${t.semester})${t.aktif ? ' - Aktif' : ''}`,
                        value: t.kd_tahun,
                    }))
                "
                placeholder="-- Pilih Tahun Ajaran --"
                :error="form.errors.kd_tahun"
            />

            <Input
                v-model="form.tgl_lulus"
                type="date"
                label="Tanggal Kelulusan Resmi"
                required
                :error="form.errors.tgl_lulus"
            />

            <Input
                v-model="form.no_ijazah"
                label="Nomor Ijazah Nasional / Surat Keterangan Lulus"
                placeholder="Contoh: DN-03/D-SMP/26/000123"
                helper="Dapat diisi nanti jika blanko ijazah belum terbit"
                :error="form.errors.no_ijazah"
            />

            <div class="flex items-center justify-end gap-3 pt-3">
                <Button type="button" variant="ghost" @click="emit('close')">
                    Batal
                </Button>
                <Button
                    type="submit"
                    variant="primary"
                    :loading="form.processing"
                >
                    Sahkan Kelulusan
                </Button>
            </div>
        </form>
    </Modal>
</template>
