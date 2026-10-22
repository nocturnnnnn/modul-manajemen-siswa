<script setup lang="ts">
import Input from '../../../ui/Input.vue';
import Select from '../../../ui/Select.vue';
import type { SelectOption } from '../../../../types';

interface FormData {
    keluarga: {
        no_kk: string;
        anak_ke: number;
        jumlah_saudara: number;
    };
    pendaftaran: {
        no_pendaftaran?: string;
        tgl_daftar?: string;
        jalur?: string;
        asal_sekolah?: string;
    };
    errors: Record<string, string>;
}

defineProps<{
    form: FormData;
}>();

const jalurOptions: SelectOption<string>[] = [
    { label: 'Zonasi', value: 'Zonasi' },
    { label: 'Prestasi', value: 'Prestasi' },
    { label: 'Afirmasi', value: 'Afirmasi' },
    { label: 'Mutasi Orang Tua', value: 'Mutasi Orang Tua' },
    { label: 'Reguler', value: 'Reguler' },
];
</script>

<template>
    <div class="space-y-6">
        <!-- Informasi Kartu Keluarga -->
        <div>
            <h4
                class="mb-3 border-b border-border-subtle pb-2 text-sm font-semibold text-slate-800"
            >
                Data Kartu Keluarga (KK)
            </h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Input
                    v-model="form.keluarga.no_kk"
                    label="Nomor Kartu Keluarga (16 Digit)"
                    placeholder="Contoh: 3307010101010001"
                    required
                    :error="form.errors['keluarga.no_kk']"
                />
                <Input
                    v-model="form.keluarga.anak_ke"
                    type="number"
                    label="Anak Ke-"
                    placeholder="1"
                    required
                    :error="form.errors['keluarga.anak_ke']"
                />
                <Input
                    v-model="form.keluarga.jumlah_saudara"
                    type="number"
                    label="Jumlah Saudara Kandung"
                    placeholder="0"
                    required
                    :error="form.errors['keluarga.jumlah_saudara']"
                />
            </div>
        </div>

        <!-- Informasi Pendaftaran Siswa -->
        <div>
            <h4
                class="mb-3 border-b border-border-subtle pb-2 text-sm font-semibold text-slate-800"
            >
                Informasi Pendaftaran Masuk
            </h4>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Input
                    v-model="form.pendaftaran.asal_sekolah"
                    label="Asal Sekolah (SD/MI)"
                    placeholder="Contoh: SD Negeri 1 Wonosobo"
                    :error="form.errors['pendaftaran.asal_sekolah']"
                />
                <Select
                    :model-value="form.pendaftaran.jalur ?? 'Reguler'"
                    label="Jalur Masuk"
                    :options="jalurOptions"
                    :error="form.errors['pendaftaran.jalur']"
                    @update:model-value="
                        (val) => (form.pendaftaran.jalur = String(val))
                    "
                />
                <Input
                    v-model="form.pendaftaran.tgl_daftar"
                    type="date"
                    label="Tanggal Pendaftaran"
                    :error="form.errors['pendaftaran.tgl_daftar']"
                />
            </div>
        </div>
    </div>
</template>
