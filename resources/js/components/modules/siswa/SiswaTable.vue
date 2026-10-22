<script setup lang="ts">
import DataTable, { type TableHeader } from '../../ui/DataTable.vue';
import Pagination from '../../ui/Pagination.vue';
import SiswaTableRow from './SiswaTableRow.vue';
import type { PaginatedData, Siswa } from '../../../types';

defineProps<{
    siswaList: PaginatedData<Siswa>;
}>();

defineEmits<{
    (e: 'delete', siswa: Siswa): void;
}>();

const headers: TableHeader[] = [
    { key: 'siswa', label: 'Nama Siswa & NIS' },
    { key: 'nisn_nik', label: 'Nomor Identitas' },
    { key: 'kelahiran', label: 'JK & TTL' },
    { key: 'agama', label: 'Agama' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: 'Aksi', align: 'right', width: 'w-48' },
];
</script>

<template>
    <div class="space-y-4">
        <DataTable
            :headers="headers"
            :items="siswaList.data"
            empty-message="Tidak ada data siswa yang cocok dengan kriteria pencarian."
        >
            <template #default="{ items }">
                <SiswaTableRow
                    v-for="siswa in items as Siswa[]"
                    :key="siswa.kd_siswa"
                    :siswa="siswa"
                    @delete="$emit('delete', $event)"
                />
            </template>
        </DataTable>

        <Pagination
            :links="siswaList.links"
            :from="siswaList.from"
            :to="siswaList.to"
            :total="siswaList.total"
        />
    </div>
</template>
