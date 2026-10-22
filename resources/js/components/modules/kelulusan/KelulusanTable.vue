<script setup lang="ts">
import Button from '../../ui/Button.vue';
import Badge from '../../ui/Badge.vue';
import Pagination from '../../ui/Pagination.vue';
import type { Kelulusan } from '../../../types/models';
import type { PaginatedData } from '../../../types/api';

defineProps<{
    kelulusanList: PaginatedData<Kelulusan>;
}>();

const emit = defineEmits<{
    (e: 'alumni', item: Kelulusan): void;
    (e: 'delete', item: Kelulusan): void;
}>();

function formatDate(dateStr?: string | null): string {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}
</script>

<template>
    <div
        class="border-surface-200 overflow-hidden rounded-xl border bg-white shadow-xs"
    >
        <div class="overflow-x-auto">
            <table class="text-surface-600 w-full text-left text-sm">
                <thead
                    class="border-surface-200 bg-surface-50 text-surface-500 border-b text-xs font-semibold tracking-wider uppercase"
                >
                    <tr>
                        <th scope="col" class="px-6 py-4">Siswa</th>
                        <th scope="col" class="px-6 py-4">Tahun Ajaran</th>
                        <th scope="col" class="px-6 py-4">Tanggal Kelulusan</th>
                        <th scope="col" class="px-6 py-4">Nomor Ijazah</th>
                        <th scope="col" class="px-6 py-4">
                            Status Direktori Alumni
                        </th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-surface-200 divide-y">
                    <tr
                        v-for="item in kelulusanList.data"
                        :key="item.kd_lulus"
                        class="hover:bg-surface-50 transition-colors"
                    >
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-100 font-semibold text-primary-700"
                                >
                                    {{ item.siswa?.nm_siswa?.charAt(0) ?? 'S' }}
                                </div>
                                <div>
                                    <div class="text-surface-900 font-medium">
                                        {{ item.siswa?.nm_siswa }}
                                    </div>
                                    <div class="text-surface-400 text-xs">
                                        NIS: {{ item.siswa?.nis }} · NISN:
                                        {{ item.siswa?.nisn }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-surface-800 font-medium">
                                {{
                                    item.tahun_ajaran
                                        ? `${item.tahun_ajaran.nm_tahun} (${item.tahun_ajaran.semester})`
                                        : '-'
                                }}
                            </span>
                        </td>
                        <td
                            class="text-surface-700 px-6 py-4 whitespace-nowrap"
                        >
                            {{ formatDate(item.tgl_lulus) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                v-if="item.no_ijazah"
                                class="bg-surface-100 text-surface-800 inline-flex items-center rounded-md px-2.5 py-1 font-mono text-xs font-medium"
                            >
                                {{ item.no_ijazah }}
                            </span>
                            <span
                                v-else
                                class="text-surface-400 text-xs italic"
                            >
                                Belum dicatat
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <Badge
                                v-if="item.alumni"
                                variant="success"
                                size="sm"
                            >
                                {{ item.alumni.kegiatan_lanjutan }}:
                                {{ item.alumni.instansi }}
                            </Badge>
                            <Badge v-else variant="warning" size="sm">
                                Belum Terdata Alumni
                            </Badge>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <Button
                                    size="sm"
                                    :variant="item.alumni ? 'ghost' : 'outline'"
                                    @click="emit('alumni', item)"
                                >
                                    {{
                                        item.alumni
                                            ? 'Ubah Alumni'
                                            : '+ Catat Alumni'
                                    }}
                                </Button>
                                <Button
                                    size="sm"
                                    variant="danger"
                                    @click="emit('delete', item)"
                                    title="Batalkan Kelulusan"
                                >
                                    Batalkan
                                </Button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="kelulusanList.data.length === 0">
                        <td
                            colspan="6"
                            class="text-surface-400 px-6 py-12 text-center"
                        >
                            <div
                                class="flex flex-col items-center justify-center gap-2"
                            >
                                <svg
                                    class="text-surface-300 h-10 w-10"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M12 14l9-5-9-5-9 5 9 5z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                                    />
                                </svg>
                                <p class="text-sm font-medium">
                                    Belum ada data kelulusan yang disahkan
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="kelulusanList.links && kelulusanList.links.length > 3"
            class="border-surface-200 border-t px-6 py-4"
        >
            <Pagination
                :links="kelulusanList.links"
                :from="kelulusanList.from"
                :to="kelulusanList.to"
                :total="kelulusanList.total"
            />
        </div>
    </div>
</template>
