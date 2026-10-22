<script setup lang="ts">
import Input from '../../../ui/Input.vue';
import Select from '../../../ui/Select.vue';
import Button from '../../../ui/Button.vue';
import type { SelectOption } from '../../../../types';

interface OrtuItem {
    kd_ortu?: number;
    hubungan: 'ayah' | 'ibu' | 'wali';
    nm_ortu: string;
    nik?: string;
    pekerjaan?: string;
    pendidikan?: string;
    no_hp?: string;
}

interface FormData {
    orang_tua: OrtuItem[];
    errors: Record<string, string>;
}

const props = defineProps<{
    form: FormData;
}>();

const hubunganOptions: SelectOption<string>[] = [
    { label: 'Ayah Kandung', value: 'ayah' },
    { label: 'Ibu Kandung', value: 'ibu' },
    { label: 'Wali Murid', value: 'wali' },
];

function addOrangTua() {
    props.form.orang_tua.push({
        hubungan:
            props.form.orang_tua.length === 0
                ? 'ayah'
                : props.form.orang_tua.length === 1
                  ? 'ibu'
                  : 'wali',
        nm_ortu: '',
        nik: '',
        pekerjaan: '',
        pendidikan: '',
        no_hp: '',
    });
}

function removeOrangTua(index: number) {
    if (props.form.orang_tua.length > 1) {
        props.form.orang_tua.splice(index, 1);
    }
}
</script>

<template>
    <div class="space-y-6">
        <div
            class="flex items-center justify-between border-b border-border-subtle pb-3"
        >
            <div>
                <h4 class="text-sm font-semibold text-slate-800">
                    Data Orang Tua & Wali Siswa
                </h4>
                <p class="text-xs text-slate-500">
                    Daftarkan minimal satu orang tua (Ayah/Ibu) atau wali siswa.
                </p>
            </div>
            <Button
                variant="secondary"
                size="sm"
                type="button"
                @click="addOrangTua"
            >
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
                Tambah Ortu/Wali
            </Button>
        </div>

        <div
            v-for="(ortu, index) in form.orang_tua"
            :key="index"
            class="relative space-y-4 rounded-xl border border-border-subtle bg-slate-50/50 p-4"
        >
            <div
                class="flex items-center justify-between border-b border-border-subtle pb-2"
            >
                <span
                    class="text-xs font-bold tracking-wider text-primary-700 uppercase"
                >
                    Data #{{ index + 1 }} • {{ ortu.hubungan.toUpperCase() }}
                </span>

                <button
                    v-if="form.orang_tua.length > 1"
                    type="button"
                    class="cursor-pointer text-xs font-medium text-rose-600 hover:text-rose-800"
                    @click="removeOrangTua(index)"
                >
                    Hapus
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Select
                    :model-value="ortu.hubungan"
                    label="Hubungan Keluarga"
                    :options="hubunganOptions"
                    required
                    :error="form.errors[`orang_tua.${index}.hubungan`]"
                    @update:model-value="
                        (val) =>
                            (ortu.hubungan = val as 'ayah' | 'ibu' | 'wali')
                    "
                />
                <Input
                    v-model="ortu.nm_ortu"
                    label="Nama Lengkap"
                    placeholder="Nama orang tua / wali"
                    required
                    :error="form.errors[`orang_tua.${index}.nm_ortu`]"
                />
                <Input
                    v-model="ortu.nik"
                    label="NIK (16 Digit)"
                    placeholder="Contoh: 3307010101010001"
                    :error="form.errors[`orang_tua.${index}.nik`]"
                />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Input
                    v-model="ortu.pekerjaan"
                    label="Pekerjaan"
                    placeholder="Contoh: PNS, Wiraswasta, Petani"
                    :error="form.errors[`orang_tua.${index}.pekerjaan`]"
                />
                <Input
                    v-model="ortu.pendidikan"
                    label="Pendidikan Terakhir"
                    placeholder="Contoh: SMA, S1, SMP"
                    :error="form.errors[`orang_tua.${index}.pendidikan`]"
                />
                <Input
                    v-model="ortu.no_hp"
                    label="Nomor HP / WhatsApp"
                    placeholder="Contoh: 081234567890"
                    :error="form.errors[`orang_tua.${index}.no_hp`]"
                />
            </div>
        </div>
    </div>
</template>
