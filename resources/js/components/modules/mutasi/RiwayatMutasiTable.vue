<script setup lang="ts">
import Badge from '../../ui/Badge.vue';
import Button from '../../ui/Button.vue';
import DataTable, { type TableHeader } from '../../ui/DataTable.vue';
import type { Mutasi } from '../../../types';

defineProps<{
    items: Mutasi[];
}>();

const emit = defineEmits<{
    (e: 'delete', mutasi: Mutasi): void;
}>();

const headers: TableHeader[] = [
    { key: 'siswa', label: 'Nama Siswa & NIS' },
    { key: 'jenis', label: 'Jenis Mutasi' },
    { key: 'tanggal', label: 'Tanggal Mutasi' },
    { key: 'no_surat', label: 'Nomor Surat Resmi' },
    { key: 'actions', label: 'Aksi', align: 'right' },
];

function getBadgeVariant(jenis: string): 'primary' | 'danger' | 'info' {
    if (jenis === 'Keluar') return 'danger';
    if (jenis === 'Masuk') return 'info';
    return 'primary';
}
</script>

<template>
    <DataTable
        :headers="headers"
        :items="items"
        empty-message="Belum ada riwayat surat mutasi siswa yang dicatat."
    >
        <template #default="{ items: rows }">
            <tr
                v-for="item in rows as Mutasi[]"
                :key="item.kd_mutasi"
                class="border-b border-border-subtle transition-colors hover:bg-primary-50/40"
            >
                <td class="px-4 py-3.5 text-xs">
                    <p class="font-bold text-slate-900">
                        {{ item.siswa?.nm_siswa }}
                    </p>
                    <p class="text-2xs text-slate-500">
                        NIS: {{ item.siswa?.nis }}
                    </p>
                </td>

                <td class="px-4 py-3.5 text-xs">
                    <Badge
                        :variant="getBadgeVariant(item.jenis_mutasi)"
                        size="sm"
                    >
                        {{ item.jenis_mutasi.toUpperCase() }}
                    </Badge>
                </td>

                <td class="px-4 py-3.5 text-xs text-slate-600">
                    {{ item.tgl_mutasi }}
                </td>

                <td
                    class="px-4 py-3.5 font-mono text-xs font-medium text-slate-800"
                >
                    {{ item.no_surat }}
                </td>

                <td class="px-4 py-3.5 text-right text-xs">
                    <Button
                        variant="ghost"
                        size="sm"
                        class="text-rose-600 hover:bg-rose-50"
                        @click="emit('delete', item)"
                    >
                        Hapus
                    </Button>
                </td>
            </tr>
        </template>
    </DataTable>
</template>
