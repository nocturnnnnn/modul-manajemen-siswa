<script setup lang="ts">
import Input from '../../../ui/Input.vue';
import Select from '../../../ui/Select.vue';
import Button from '../../../ui/Button.vue';
import type { SelectOption } from '../../../../types';

interface KontakItem {
    kd_kontak?: number;
    jenis_kontak: string;
    nomor: string;
}

interface FormData {
    kontak: KontakItem[];
    errors: Record<string, string>;
}

const props = defineProps<{
    form: FormData;
}>();

const jenisOptions: SelectOption<string>[] = [
    { label: 'WhatsApp', value: 'WhatsApp' },
    { label: 'Nomor HP', value: 'HP' },
    { label: 'Telepon Rumah', value: 'Rumah' },
    { label: 'Lainnya', value: 'Lainnya' },
];

function addKontak() {
    props.form.kontak.push({
        jenis_kontak: 'WhatsApp',
        nomor: '',
    });
}

function removeKontak(index: number) {
    props.form.kontak.splice(index, 1);
}
</script>

<template>
    <div class="space-y-6">
        <div
            class="flex items-center justify-between border-b border-border-subtle pb-3"
        >
            <div>
                <h4 class="text-sm font-semibold text-slate-800">
                    Nomor Kontak & Saluran Komunikasi
                </h4>
                <p class="text-xs text-slate-500">
                    Tambahkan nomor ponsel, WhatsApp, atau telepon rumah untuk
                    korespondensi darurat.
                </p>
            </div>
            <Button
                variant="secondary"
                size="sm"
                type="button"
                @click="addKontak"
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
                Tambah Kontak
            </Button>
        </div>

        <div
            v-if="form.kontak.length === 0"
            class="rounded-xl border border-dashed border-border-subtle p-6 text-center text-xs text-slate-400"
        >
            Belum ada nomor kontak yang ditambahkan. Klik tombol di atas untuk
            menambah kontak.
        </div>

        <div
            v-for="(item, index) in form.kontak"
            :key="index"
            class="flex flex-col items-end gap-3 rounded-xl border border-border-subtle bg-slate-50/50 p-4 sm:flex-row"
        >
            <div class="w-full sm:w-1/3">
                <Select
                    :model-value="item.jenis_kontak"
                    label="Jenis Kontak"
                    :options="jenisOptions"
                    required
                    :error="form.errors[`kontak.${index}.jenis_kontak`]"
                    @update:model-value="
                        (val) => (item.jenis_kontak = String(val))
                    "
                />
            </div>
            <div class="w-full sm:w-2/3">
                <Input
                    v-model="item.nomor"
                    label="Nomor Telepon / Akun"
                    placeholder="Contoh: 081234567890"
                    required
                    :error="form.errors[`kontak.${index}.nomor`]"
                />
            </div>
            <div class="pb-0.5">
                <Button
                    variant="danger"
                    size="md"
                    type="button"
                    @click="removeKontak(index)"
                >
                    Hapus
                </Button>
            </div>
        </div>
    </div>
</template>
