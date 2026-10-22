<script setup lang="ts">
import Select from '../../ui/Select.vue';
import type { Kelas, TahunAjaran } from '../../../types';

defineProps<{
    tahunAjaranList: TahunAjaran[];
    selectedTahunId: number | '';
    kelasList: Kelas[];
    selectedKelasId: number | '';
}>();

const emit = defineEmits<{
    (e: 'update:selectedTahunId', value: number): void;
    (e: 'update:selectedKelasId', value: number): void;
}>();
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <Select
            :model-value="selectedTahunId"
            label="Tahun Ajaran Evaluasi"
            :options="
                tahunAjaranList.map((t) => ({
                    label: `${t.nm_tahun} (${t.semester})${t.aktif ? ' - Tahun Aktif' : ''}`,
                    value: t.kd_tahun,
                }))
            "
            @update:model-value="emit('update:selectedTahunId', Number($event))"
        />

        <Select
            :model-value="selectedKelasId"
            label="Rombel Kelas Asal yang Dievaluasi"
            :options="
                kelasList.map((k) => ({
                    label: `Kelas ${k.nm_kelas} (Tingkat ${k.tingkat})`,
                    value: k.kd_kelas,
                }))
            "
            @update:model-value="emit('update:selectedKelasId', Number($event))"
        />
    </div>
</template>
