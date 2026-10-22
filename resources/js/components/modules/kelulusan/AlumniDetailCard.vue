<script setup lang="ts">
import Badge from '../../ui/Badge.vue';
import Button from '../../ui/Button.vue';
import type { Alumni } from '../../../types/models';

defineProps<{
    alumni: Alumni;
}>();

const emit = defineEmits<{
    (e: 'edit', alumni: Alumni): void;
    (e: 'delete', alumni: Alumni): void;
}>();

function getBadgeVariant(
    kegiatan: string,
): 'primary' | 'secondary' | 'success' | 'warning' | 'info' {
    if (kegiatan.includes('SMA')) return 'primary';
    if (kegiatan.includes('SMK')) return 'secondary';
    if (kegiatan.includes('Bekerja')) return 'success';
    return 'info';
}
</script>

<template>
    <div
        class="border-surface-200 flex flex-col justify-between rounded-xl border bg-white p-5 shadow-xs transition-shadow hover:shadow-md"
    >
        <div>
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-primary-100 font-bold text-primary-700"
                    >
                        {{
                            alumni.kelulusan?.siswa?.nm_siswa?.charAt(0) ?? 'A'
                        }}
                    </div>
                    <div>
                        <h4 class="text-surface-900 font-semibold">
                            {{ alumni.kelulusan?.siswa?.nm_siswa }}
                        </h4>
                        <p class="text-surface-400 text-xs">
                            NIS: {{ alumni.kelulusan?.siswa?.nis }} · NISN:
                            {{ alumni.kelulusan?.siswa?.nisn }}
                        </p>
                    </div>
                </div>

                <Badge
                    :variant="getBadgeVariant(alumni.kegiatan_lanjutan)"
                    size="sm"
                >
                    {{ alumni.kegiatan_lanjutan }}
                </Badge>
            </div>

            <div class="bg-surface-50 mt-4 space-y-2 rounded-lg p-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-surface-500"
                        >Sekolah/Instansi Lanjutan:</span
                    >
                    <span class="text-surface-900 text-right font-semibold">{{
                        alumni.instansi
                    }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-500">Tahun Kelulusan:</span>
                    <span class="text-surface-700 font-medium">
                        {{ alumni.kelulusan?.tahun_ajaran?.nm_tahun ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-500">No. Ijazah:</span>
                    <span class="text-surface-700 font-mono">
                        {{ alumni.kelulusan?.no_ijazah || 'Belum diisi' }}
                    </span>
                </div>
                <div
                    v-if="alumni.kontak"
                    class="border-surface-200/60 flex justify-between border-t pt-1.5"
                >
                    <span class="text-surface-500">Kontak:</span>
                    <a
                        :href="`tel:${alumni.kontak}`"
                        class="font-medium text-primary-600 hover:underline"
                    >
                        {{ alumni.kontak }}
                    </a>
                </div>
            </div>
        </div>

        <div
            class="border-surface-100 mt-4 flex items-center justify-end gap-2 border-t pt-3"
        >
            <Button size="sm" variant="ghost" @click="emit('edit', alumni)">
                Ubah
            </Button>
            <Button size="sm" variant="danger" @click="emit('delete', alumni)">
                Hapus
            </Button>
        </div>
    </div>
</template>
