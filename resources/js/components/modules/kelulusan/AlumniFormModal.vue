<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../../ui/Modal.vue';
import Button from '../../ui/Button.vue';
import Input from '../../ui/Input.vue';
import Select from '../../ui/Select.vue';
import type { Kelulusan } from '../../../types/models';

const props = defineProps<{
    show: boolean;
    kelulusanItem?: Kelulusan | null;
    lulusanOptions?: Kelulusan[];
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm<{
    kd_lulus: number | '';
    kegiatan_lanjutan: string;
    instansi: string;
    kontak: string;
}>({
    kd_lulus: '',
    kegiatan_lanjutan: 'Melanjutkan SMA',
    instansi: '',
    kontak: '',
});

const kegiatanOptions = [
    { label: 'Melanjutkan SMA', value: 'Melanjutkan SMA' },
    { label: 'Melanjutkan SMK', value: 'Melanjutkan SMK' },
    {
        label: 'Melanjutkan MA / Pesantren',
        value: 'Melanjutkan MA / Pesantren',
    },
    { label: 'Bekerja / Wirausaha', value: 'Bekerja / Wirausaha' },
    { label: 'Lainnya', value: 'Lainnya' },
];

watch(
    () => props.show,
    (open) => {
        if (open) {
            form.clearErrors();
            if (props.kelulusanItem) {
                form.kd_lulus = props.kelulusanItem.kd_lulus;
                form.kegiatan_lanjutan =
                    props.kelulusanItem.alumni?.kegiatan_lanjutan ||
                    'Melanjutkan SMA';
                form.instansi = props.kelulusanItem.alumni?.instansi || '';
                form.kontak = props.kelulusanItem.alumni?.kontak || '';
            } else if (
                props.lulusanOptions &&
                props.lulusanOptions.length > 0
            ) {
                form.kd_lulus = props.lulusanOptions[0].kd_lulus;
                form.kegiatan_lanjutan = 'Melanjutkan SMA';
                form.instansi = '';
                form.kontak = '';
            } else {
                form.kd_lulus = '';
                form.kegiatan_lanjutan = 'Melanjutkan SMA';
                form.instansi = '';
                form.kontak = '';
            }
        }
    },
);

function handleSubmit() {
    form.post('/alumni', {
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
        :title="
            kelulusanItem?.alumni
                ? 'Ubah Data Pelacakan Alumni'
                : 'Catat Data Alumni Baru'
        "
        size="md"
        @close="emit('close')"
    >
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <div
                v-if="kelulusanItem?.siswa"
                class="border-surface-200 bg-surface-50 rounded-lg border p-3"
            >
                <div
                    class="text-surface-500 text-xs font-semibold tracking-wider uppercase"
                >
                    Siswa Lulusan Terpilih
                </div>
                <div class="text-surface-900 mt-1 font-medium">
                    {{ kelulusanItem.siswa.nm_siswa }}
                </div>
                <div class="text-surface-500 text-xs">
                    NIS: {{ kelulusanItem.siswa.nis }} · Ijazah:
                    {{ kelulusanItem.no_ijazah || 'Belum diisi' }}
                </div>
            </div>

            <Select
                v-else-if="lulusanOptions && lulusanOptions.length > 0"
                v-model="form.kd_lulus"
                label="Pilih Siswa Lulusan"
                required
                :options="
                    lulusanOptions.map((k) => ({
                        label: `${k.siswa?.nm_siswa} (NIS: ${k.siswa?.nis}) - Lulus ${k.tgl_lulus}`,
                        value: k.kd_lulus,
                    }))
                "
                placeholder="-- Pilih Siswa Lulus --"
                :error="form.errors.kd_lulus"
            />

            <Select
                v-model="form.kegiatan_lanjutan"
                label="Kegiatan / Studi Lanjutan"
                required
                :options="kegiatanOptions"
                :error="form.errors.kegiatan_lanjutan"
            />

            <Input
                v-model="form.instansi"
                label="Nama Sekolah Lanjutan / Instansi"
                placeholder="Contoh: SMA Negeri 1 Wonosobo / SMK Kristen 1"
                required
                :error="form.errors.instansi"
            />

            <Input
                v-model="form.kontak"
                label="Nomor Telepon / WhatsApp Terkini"
                placeholder="Contoh: 081234567890"
                helper="Opsional untuk kemudahan komunikasi ikatan alumni"
                :error="form.errors.kontak"
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
                    Simpan Data Alumni
                </Button>
            </div>
        </form>
    </Modal>
</template>
