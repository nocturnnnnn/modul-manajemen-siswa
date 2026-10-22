<script setup lang="ts">
import { ref } from 'vue';
import FileUpload from '../../../ui/FileUpload.vue';

interface FormData {
    foto: File | null;
    errors: Record<string, string>;
}

const props = defineProps<{
    form: FormData;
    currentFotoUrl?: string | null;
}>();

const previewUrl = ref<string | null>(null);

function handleFileUpdate(file: File | null) {
    props.form.foto = file;
    if (file) {
        previewUrl.value = URL.createObjectURL(file);
    } else {
        previewUrl.value = null;
    }
}
</script>

<template>
    <div class="space-y-6">
        <div>
            <h4 class="mb-1 text-sm font-semibold text-slate-800">
                Pas Foto Siswa
            </h4>
            <p class="text-xs text-slate-500">
                Unggah pas foto formal siswa berseragam sekolah dengan latar
                belakang merah atau biru (JPG/PNG, Maks. 2MB).
            </p>
        </div>

        <div class="flex flex-col items-center gap-6 sm:flex-row">
            <!-- Avatar Display / Preview -->
            <div
                class="flex h-32 w-32 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-primary-300 bg-primary-50/60 shadow-xs"
            >
                <img
                    v-if="previewUrl"
                    :src="previewUrl"
                    alt="Preview Foto Baru"
                    class="h-full w-full object-cover"
                />
                <img
                    v-else-if="currentFotoUrl"
                    :src="`/storage/${currentFotoUrl}`"
                    alt="Foto Profil Siswa"
                    class="h-full w-full object-cover"
                />
                <div v-else class="p-3 text-center text-slate-400">
                    <svg
                        class="mx-auto mb-1 h-10 w-10 text-primary-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                    <span class="text-2xs font-medium">Belum Ada Foto</span>
                </div>
            </div>

            <!-- File Upload Dropzone -->
            <div class="w-full flex-1">
                <FileUpload
                    :model-value="form.foto"
                    label="Pilih Berkas Pas Foto"
                    accept=".jpg,.jpeg,.png"
                    :max-size-mb="2"
                    helper-text="Format JPG atau PNG (Maks 2MB)"
                    :error="form.errors.foto"
                    @update:model-value="handleFileUpdate"
                />
            </div>
        </div>
    </div>
</template>
