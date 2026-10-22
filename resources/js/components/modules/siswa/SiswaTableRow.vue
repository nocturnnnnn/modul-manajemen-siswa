<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import SiswaStatusBadge from './SiswaStatusBadge.vue';
import Button from '../../ui/Button.vue';
import type { Siswa } from '../../../types';

defineProps<{
    siswa: Siswa;
}>();

defineEmits<{
    (e: 'delete', siswa: Siswa): void;
}>();
</script>

<template>
    <tr
        class="border-b border-border-subtle transition-colors hover:bg-primary-50/40"
    >
        <!-- Avatar & Identitas -->
        <td class="px-4 py-3.5">
            <div class="flex items-center gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full border border-primary-200 bg-primary-100 text-sm font-bold text-primary-700"
                >
                    <img
                        v-if="siswa.foto"
                        :src="`/storage/${siswa.foto}`"
                        :alt="siswa.nm_siswa"
                        class="h-full w-full object-cover"
                    />
                    <span v-else>
                        {{ siswa.nm_siswa.charAt(0) }}
                    </span>
                </div>
                <div>
                    <Link
                        :href="`/siswa/${siswa.kd_siswa}`"
                        class="text-sm font-semibold text-slate-900 transition-colors hover:text-primary-600"
                    >
                        {{ siswa.nm_siswa }}
                    </Link>
                    <p class="font-mono text-xs text-slate-500">
                        NIS: {{ siswa.nis }}
                    </p>
                </div>
            </div>
        </td>

        <!-- NISN & NIK -->
        <td class="px-4 py-3.5 font-mono text-xs text-slate-700">
            <p><span class="text-slate-400">NISN:</span> {{ siswa.nisn }}</p>
            <p><span class="text-slate-400">NIK:</span> {{ siswa.nik }}</p>
        </td>

        <!-- Gender & Tempat/Tgl Lahir -->
        <td class="px-4 py-3.5 text-xs text-slate-700">
            <p class="font-medium text-slate-800">{{ siswa.jenis_kelamin }}</p>
            <p class="text-slate-500">
                {{ siswa.tempat_lahir }}, {{ siswa.tgl_lahir }}
            </p>
        </td>

        <!-- Agama -->
        <td class="px-4 py-3.5 text-xs text-slate-700">
            {{ siswa.agama?.nm_agama ?? '-' }}
        </td>

        <!-- Status -->
        <td class="px-4 py-3.5 text-xs">
            <SiswaStatusBadge :status="siswa.status_siswa" />
        </td>

        <!-- Aksi -->
        <td class="px-4 py-3.5 text-right">
            <div class="flex items-center justify-end gap-1.5">
                <Link :href="`/siswa/${siswa.kd_siswa}`">
                    <Button variant="outline" size="sm"> Detail </Button>
                </Link>
                <Link :href="`/siswa/${siswa.kd_siswa}/edit`">
                    <Button variant="secondary" size="sm"> Edit </Button>
                </Link>
                <Button
                    variant="danger"
                    size="sm"
                    @click="$emit('delete', siswa)"
                >
                    Hapus
                </Button>
            </div>
        </td>
    </tr>
</template>
