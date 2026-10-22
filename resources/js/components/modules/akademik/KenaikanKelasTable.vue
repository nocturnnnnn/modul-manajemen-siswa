<script setup lang="ts">
import Badge from '../../ui/Badge.vue';
import Select from '../../ui/Select.vue';
import type { Kelas, Siswa } from '../../../types';

export interface EvaluasiItem {
    kd_siswa: number;
    status: 'naik' | 'tinggal';
    kd_kelas_tujuan: number | '';
    [key: string]: string | number;
}

const props = defineProps<{
    siswaList: Siswa[];
    evaluasiItems: Record<number, EvaluasiItem>;
    targetKelasNaik: Kelas[];
    targetKelasTinggal: Kelas[];
    isGrade9: boolean;
}>();

const emit = defineEmits<{
    (e: 'updateItem', item: EvaluasiItem): void;
}>();

function setStatus(kdSiswa: number, status: 'naik' | 'tinggal') {
    const defaultTarget =
        status === 'naik'
            ? (props.targetKelasNaik[0]?.kd_kelas ?? '')
            : (props.targetKelasTinggal[0]?.kd_kelas ?? '');

    emit('updateItem', {
        kd_siswa: kdSiswa,
        status,
        kd_kelas_tujuan: defaultTarget,
    });
}

function setTarget(kdSiswa: number, kdKelas: number) {
    const current = props.evaluasiItems[kdSiswa];
    if (current) {
        emit('updateItem', {
            ...current,
            kd_kelas_tujuan: kdKelas,
        });
    }
}
</script>

<template>
    <div
        class="overflow-x-auto rounded-xl border border-border-subtle bg-white shadow-2xs"
    >
        <table
            class="min-w-full divide-y divide-border-subtle text-left text-xs"
        >
            <thead
                class="bg-surface-subtle font-semibold tracking-wider text-slate-700 uppercase"
            >
                <tr>
                    <th class="px-4 py-3.5">No</th>
                    <th class="px-4 py-3.5">Nama Siswa & NIS</th>
                    <th class="px-4 py-3.5">Keputusan Evaluasi</th>
                    <th class="px-4 py-3.5">Rombel Kelas Tujuan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border-subtle bg-white">
                <tr v-if="siswaList.length === 0">
                    <td colspan="4" class="py-10 text-center text-slate-400">
                        Tidak ada siswa pada rombel kelas ini yang siap
                        dievaluasi.
                    </td>
                </tr>

                <tr
                    v-for="(siswa, idx) in siswaList"
                    :key="siswa.kd_siswa"
                    class="transition-colors hover:bg-primary-50/30"
                >
                    <td class="px-4 py-3 font-mono text-slate-400">
                        {{ idx + 1 }}
                    </td>

                    <td class="px-4 py-3">
                        <p class="font-bold text-slate-900">
                            {{ siswa.nm_siswa }}
                        </p>
                        <p class="text-2xs text-slate-500">
                            NIS: {{ siswa.nis }} • NISN: {{ siswa.nisn }}
                        </p>
                    </td>

                    <!-- Decision Toggle -->
                    <td class="px-4 py-3">
                        <div v-if="isGrade9" class="space-y-1">
                            <Badge variant="info" size="sm">
                                Calon Kelulusan (Tingkat 9)
                            </Badge>
                            <p class="text-2xs text-slate-500">
                                Siswa tingkat 9 diproses pada Modul Kelulusan.
                                Jika tidak lulus, pilih Tinggal Kelas.
                            </p>
                        </div>

                        <div
                            class="inline-flex rounded-lg border border-border-subtle bg-slate-50 p-0.5"
                        >
                            <button
                                v-if="!isGrade9"
                                type="button"
                                class="cursor-pointer rounded-md px-3 py-1 font-semibold transition-all"
                                :class="[
                                    evaluasiItems[siswa.kd_siswa]?.status ===
                                    'naik'
                                        ? 'bg-emerald-600 text-white shadow-xs'
                                        : 'text-slate-600 hover:text-slate-900',
                                ]"
                                @click="setStatus(siswa.kd_siswa, 'naik')"
                            >
                                Naik Kelas
                            </button>
                            <button
                                type="button"
                                class="cursor-pointer rounded-md px-3 py-1 font-semibold transition-all"
                                :class="[
                                    evaluasiItems[siswa.kd_siswa]?.status ===
                                    'tinggal'
                                        ? 'bg-rose-600 text-white shadow-xs'
                                        : 'text-slate-600 hover:text-slate-900',
                                ]"
                                @click="setStatus(siswa.kd_siswa, 'tinggal')"
                            >
                                Tinggal Kelas
                            </button>
                        </div>
                    </td>

                    <!-- Target Class -->
                    <td class="px-4 py-3">
                        <div class="max-w-xs">
                            <Select
                                :model-value="
                                    evaluasiItems[siswa.kd_siswa]
                                        ?.kd_kelas_tujuan ?? ''
                                "
                                :options="
                                    (evaluasiItems[siswa.kd_siswa]?.status ===
                                    'naik'
                                        ? targetKelasNaik
                                        : targetKelasTinggal
                                    ).map((k) => ({
                                        label: `Kelas ${k.nm_kelas} (Tingkat ${k.tingkat})`,
                                        value: k.kd_kelas,
                                    }))
                                "
                                placeholder="-- Pilih Kelas Tujuan --"
                                @update:model-value="
                                    setTarget(siswa.kd_siswa, Number($event))
                                "
                            />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
