<script setup lang="ts">
import Modal from '../../ui/Modal.vue';
import Button from '../../ui/Button.vue';

defineProps<{
    show: boolean;
    namaKelasAsal: string;
    namaTahunAjaran: string;
    countNaik: number;
    countTinggal: number;
    isLoading?: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Modal
        :show="show"
        title="Konfirmasi Pengesahan Kenaikan Kelas"
        size="md"
        @close="emit('close')"
    >
        <div class="space-y-4 text-xs">
            <div
                class="space-y-2 rounded-xl border border-border-subtle bg-surface-subtle p-3.5"
            >
                <div class="flex justify-between">
                    <span class="text-slate-500">Rombel Kelas Asal:</span>
                    <strong class="text-slate-800">{{ namaKelasAsal }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tahun Ajaran:</span>
                    <strong class="text-slate-800">{{
                        namaTahunAjaran
                    }}</strong>
                </div>
            </div>

            <!-- Summary Breakdown -->
            <div class="grid grid-cols-2 gap-3">
                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center"
                >
                    <p
                        class="text-2xs font-semibold tracking-wider text-emerald-700 uppercase"
                    >
                        Siswa Naik Kelas
                    </p>
                    <p class="mt-1 text-2xl font-bold text-emerald-800">
                        {{ countNaik }}
                    </p>
                </div>

                <div
                    class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-center"
                >
                    <p
                        class="text-2xs font-semibold tracking-wider text-rose-700 uppercase"
                    >
                        Siswa Tinggal Kelas
                    </p>
                    <p class="mt-1 text-2xl font-bold text-rose-800">
                        {{ countTinggal }}
                    </p>
                </div>
            </div>

            <p class="leading-relaxed text-slate-500">
                Tindakan ini akan mencatat riwayat kenaikan kelas resmi pada
                database dan memperbarui penempatan rombel siswa untuk tahun
                ajaran berikutnya.
            </p>

            <div
                class="flex items-center justify-end gap-2 border-t border-border-subtle pt-3"
            >
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="emit('close')"
                >
                    Batal
                </Button>
                <Button
                    type="button"
                    variant="primary"
                    size="sm"
                    :loading="isLoading"
                    @click="emit('confirm')"
                >
                    Sahkan Evaluasi
                </Button>
            </div>
        </div>
    </Modal>
</template>
