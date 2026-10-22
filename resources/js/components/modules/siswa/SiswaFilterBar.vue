<script setup lang="ts">
import { ref, watch } from 'vue';
import Card from '../../ui/Card.vue';
import Input from '../../ui/Input.vue';
import Select from '../../ui/Select.vue';
import Button from '../../ui/Button.vue';
import type { Agama, SelectOption } from '../../../types';

interface Filters {
    search?: string;
    status?: string;
    agama?: string | number;
}

const props = defineProps<{
    filters: Filters;
    agamaList: Agama[];
}>();

const emit = defineEmits<{
    (e: 'filter', val: Filters): void;
    (e: 'reset'): void;
}>();

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const agama = ref(props.filters.agama ? String(props.filters.agama) : '');

const statusOptions: SelectOption<string>[] = [
    { label: 'Semua Status Siswa', value: '' },
    { label: 'Aktif', value: 'aktif' },
    { label: 'Mutasi', value: 'mutasi' },
    { label: 'Pindah Sekolah', value: 'pindah' },
    { label: 'Lulus', value: 'lulus' },
    { label: 'Alumni', value: 'alumni' },
];

const agamaOptions = [
    { label: 'Semua Agama', value: '' },
    ...props.agamaList.map((a) => ({
        label: a.nm_agama,
        value: String(a.kd_agama),
    })),
];

let debounceTimeout: ReturnType<typeof setTimeout>;

function emitFilter() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        emit('filter', {
            search: search.value || undefined,
            status: status.value || undefined,
            agama: agama.value || undefined,
        });
    }, 300);
}

function handleReset() {
    search.value = '';
    status.value = '';
    agama.value = '';
    emit('reset');
}

watch([status, agama], () => {
    emitFilter();
});
</script>

<template>
    <Card padding="sm" class="mb-5">
        <div
            class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-4"
        >
            <div class="lg:col-span-2">
                <Input
                    v-model="search"
                    placeholder="Cari Nama, NIS, NISN, atau NIK siswa..."
                    @input="emitFilter"
                >
                    <template #leading>
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
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </template>
                </Input>
            </div>

            <div>
                <Select
                    :model-value="status"
                    :options="statusOptions"
                    placeholder="Pilih Status"
                    @update:model-value="
                        (val) => {
                            status = String(val);
                        }
                    "
                />
            </div>

            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <Select
                        :model-value="agama"
                        :options="agamaOptions"
                        placeholder="Pilih Agama"
                        @update:model-value="
                            (val) => {
                                agama = String(val);
                            }
                        "
                    />
                </div>

                <Button
                    variant="outline"
                    size="md"
                    title="Reset Filter"
                    @click="handleReset"
                >
                    Reset
                </Button>
            </div>
        </div>
    </Card>
</template>
