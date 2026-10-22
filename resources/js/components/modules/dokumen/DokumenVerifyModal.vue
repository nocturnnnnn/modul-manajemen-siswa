<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../../ui/Modal.vue';
import Button from '../../ui/Button.vue';
import Textarea from '../../ui/Textarea.vue';
import DokumenStatusBadge from './DokumenStatusBadge.vue';
import type { DokumenSiswa, StatusVerifikasiDokumen } from '../../../types';

const props = defineProps<{
    isOpen: boolean;
    dokumen: DokumenSiswa | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm<{
    status_verifikasi: StatusVerifikasiDokumen;
    catatan_verifikasi: string;
}>({
    status_verifikasi: 'terverifikasi',
    catatan_verifikasi: '',
});

watch(
    () => props.dokumen,
    (newDoc) => {
        if (newDoc) {
            form.status_verifikasi =
                newDoc.status_verifikasi === 'belum'
                    ? 'terverifikasi'
                    : newDoc.status_verifikasi;
            form.catatan_verifikasi = newDoc.catatan_verifikasi || '';
        }
    },
    { immediate: true },
);

function handleSubmit() {
    if (!props.dokumen) return;

    form.put(`/dokumen/${props.dokumen.kd_dokumen}/verify`, {
        preserveScroll: true,
        onSuccess: () => {
            emit('close');
        },
    });
}
</script>

<template>
    <Modal
        :show="isOpen"
        title="Verifikasi Dokumen Digital"
        size="md"
        @close="$emit('close')"
    >
        <div v-if="dokumen" class="space-y-4">
            <!-- Document Meta Details -->
            <div
                class="rounded-xl border border-border-subtle bg-surface-subtle p-3.5 text-xs"
            >
                <div class="mb-2 flex items-center justify-between">
                    <span class="font-bold text-slate-800">
                        {{ dokumen.siswa?.nm_siswa }}
                    </span>
                    <DokumenStatusBadge :status="dokumen.status_verifikasi" />
                </div>
                <div class="grid grid-cols-2 gap-2 text-slate-600">
                    <div>
                        <span class="text-slate-400">NIS:</span>
                        {{ dokumen.siswa?.nis }}
                    </div>
                    <div>
                        <span class="text-slate-400">Jenis Dokumen:</span>
                        {{ dokumen.jenis?.nm_jenis }}
                    </div>
                    <div>
                        <span class="text-slate-400">Nomor Berkas:</span>
                        {{ dokumen.nomor_dokumen || '-' }}
                    </div>
                    <div>
                        <span class="text-slate-400">Waktu Upload:</span>
                        {{ dokumen.tgl_upload }}
                    </div>
                </div>

                <div
                    class="mt-3 flex items-center justify-end border-t border-border-subtle/80 pt-2"
                >
                    <a
                        :href="`/storage/${dokumen.file_path}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 font-semibold text-primary-600 hover:text-primary-800"
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
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                            />
                        </svg>
                        <span>Buka / Unduh Berkas Asli</span>
                    </a>
                </div>
            </div>

            <!-- Verification Action Form -->
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div>
                    <label
                        class="mb-1.5 block text-xs font-semibold text-slate-700"
                    >
                        Hasil Keputusan Verifikasi
                        <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border p-2.5 text-xs font-semibold transition-all"
                            :class="[
                                form.status_verifikasi === 'terverifikasi'
                                    ? 'border-emerald-500 bg-emerald-50 text-emerald-800 ring-2 ring-emerald-500/20'
                                    : 'border-border-subtle bg-white text-slate-600 hover:bg-slate-50',
                            ]"
                        >
                            <input
                                type="radio"
                                v-model="form.status_verifikasi"
                                value="terverifikasi"
                                class="hidden"
                            />
                            <span>Setujui (Sah)</span>
                        </label>

                        <label
                            class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border p-2.5 text-xs font-semibold transition-all"
                            :class="[
                                form.status_verifikasi === 'ditolak'
                                    ? 'border-rose-500 bg-rose-50 text-rose-800 ring-2 ring-rose-500/20'
                                    : 'border-border-subtle bg-white text-slate-600 hover:bg-slate-50',
                            ]"
                        >
                            <input
                                type="radio"
                                v-model="form.status_verifikasi"
                                value="ditolak"
                                class="hidden"
                            />
                            <span>Tolak Berkas</span>
                        </label>
                    </div>
                </div>

                <Textarea
                    v-model="form.catatan_verifikasi"
                    label="Catatan Verifikasi / Alasan Penolakan"
                    :placeholder="
                        form.status_verifikasi === 'ditolak'
                            ? 'Wajib diisi jika ditolak (misal: foto kabur, nomor tidak cocok)...'
                            : 'Catatan opsional...'
                    "
                    :error="form.errors.catatan_verifikasi"
                    :rows="3"
                />

                <div
                    class="flex items-center justify-end gap-2 border-t border-border-subtle pt-3"
                >
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="$emit('close')"
                    >
                        Batal
                    </Button>
                    <Button
                        type="submit"
                        :variant="
                            form.status_verifikasi === 'ditolak'
                                ? 'danger'
                                : 'primary'
                        "
                        size="sm"
                        :loading="form.processing"
                    >
                        Simpan Keputusan
                    </Button>
                </div>
            </form>
        </div>
    </Modal>
</template>
