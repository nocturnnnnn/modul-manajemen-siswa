<script setup lang="ts">
import Button from '../../ui/Button.vue';
import DokumenStatusBadge from './DokumenStatusBadge.vue';
import type { DokumenSiswa } from '../../../types';

defineProps<{
    dokumenList: DokumenSiswa[];
    canVerify?: boolean;
}>();

const emit = defineEmits<{
    (e: 'verify', doc: DokumenSiswa): void;
    (e: 'delete', doc: DokumenSiswa): void;
}>();
</script>

<template>
    <div class="space-y-3">
        <div
            v-if="dokumenList.length === 0"
            class="flex flex-col items-center justify-center rounded-xl border border-dashed border-border-subtle bg-slate-50/50 py-10 text-center"
        >
            <div
                class="mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 text-primary-600"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
            </div>
            <p class="text-xs font-medium text-slate-600">
                Belum ada dokumen digital yang diunggah.
            </p>
            <p class="text-2xs text-slate-400">
                Unggah dokumen persyaratan seperti Kartu Keluarga, Akta
                Kelahiran, dll.
            </p>
        </div>

        <div
            v-for="doc in dokumenList"
            :key="doc.kd_dokumen"
            class="flex flex-col gap-3 rounded-xl border border-border-subtle bg-white p-3.5 transition-shadow hover:shadow-xs sm:flex-row sm:items-center sm:justify-between"
        >
            <!-- Left Info -->
            <div class="flex items-start gap-3">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-primary-200 bg-primary-50 text-xs font-bold text-primary-700"
                >
                    {{ doc.jenis?.kode ?? 'DOC' }}
                </div>
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-slate-900">
                            {{ doc.jenis?.nm_jenis ?? 'Dokumen Digital' }}
                        </span>
                        <DokumenStatusBadge :status="doc.status_verifikasi" />
                    </div>

                    <p class="text-2xs text-slate-500">
                        <span class="text-slate-400">Nomor:</span>
                        {{ doc.nomor_dokumen || '-' }} •
                        <span class="text-slate-400">Diunggah:</span>
                        {{ doc.tgl_upload }}
                    </p>

                    <p
                        v-if="
                            doc.status_verifikasi === 'ditolak' &&
                            doc.catatan_verifikasi
                        "
                        class="text-2xs rounded-md bg-rose-50 px-2 py-1 font-medium text-rose-700"
                    >
                        <strong>Catatan Penolakan:</strong>
                        {{ doc.catatan_verifikasi }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 self-end sm:self-center">
                <a
                    :href="`/storage/${doc.file_path}`"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <Button variant="outline" size="sm"> Lihat Berkas </Button>
                </a>

                <Button
                    v-if="canVerify"
                    variant="secondary"
                    size="sm"
                    @click="emit('verify', doc)"
                >
                    Verifikasi
                </Button>

                <Button
                    variant="ghost"
                    size="sm"
                    class="text-rose-600 hover:bg-rose-50 hover:text-rose-700"
                    @click="emit('delete', doc)"
                >
                    Hapus
                </Button>
            </div>
        </div>
    </div>
</template>
