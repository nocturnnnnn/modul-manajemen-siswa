<script setup lang="ts">
import { ref, computed } from 'vue';

interface Props {
    modelValue: File | null;
    label?: string;
    accept?: string;
    maxSizeMb?: number;
    error?: string;
    helperText?: string;
    required?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    accept: '.pdf,.jpg,.jpeg,.png',
    maxSizeMb: 3,
    error: '',
    helperText: 'PDF, JPG, atau PNG (Maks 3MB)',
    required: false,
});

const emit = defineEmits<{
    (e: 'update:modelValue', file: File | null): void;
}>();

const isDragging = ref(false);
const localError = ref('');
const fileInput = ref<HTMLInputElement | null>(null);

const displayError = computed(() => props.error || localError.value);

function handleFileSelect(file: File | undefined) {
    localError.value = '';
    if (!file) {
        emit('update:modelValue', null);
        return;
    }

    if (file.size > props.maxSizeMb * 1024 * 1024) {
        localError.value = `Ukuran file melebihi batas maksimal ${props.maxSizeMb}MB.`;
        return;
    }

    emit('update:modelValue', file);
}

function onDrop(e: DragEvent) {
    isDragging.value = false;
    const file = e.dataTransfer?.files[0];
    handleFileSelect(file);
}

function onChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    handleFileSelect(file);
}

function clearFile() {
    localError.value = '';
    if (fileInput.value) fileInput.value.value = '';
    emit('update:modelValue', null);
}
</script>

<template>
    <div class="w-full space-y-1.5">
        <label v-if="label" class="block text-sm font-medium text-slate-700">
            {{ label }}
            <span v-if="required" class="text-rose-500">*</span>
        </label>

        <!-- Selected File Preview -->
        <div
            v-if="modelValue"
            class="flex items-center justify-between p-3.5 bg-primary-50/60 rounded-xl border border-primary-200"
        >
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="h-9 w-9 shrink-0 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="truncate">
                    <p class="text-sm font-medium text-slate-800 truncate">
                        {{ modelValue.name }}
                    </p>
                    <p class="text-xs text-slate-500">
                        {{ (modelValue.size / 1024 / 1024).toFixed(2) }} MB
                    </p>
                </div>
            </div>

            <button
                type="button"
                class="rounded-lg p-1 text-slate-400 hover:bg-white hover:text-rose-600 transition-colors cursor-pointer"
                @click="clearFile"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Dropzone -->
        <div
            v-else
            class="flex flex-col items-center justify-center rounded-xl border-2 border-dashed p-6 text-center transition-colors cursor-pointer"
            :class="[
                isDragging
                    ? 'border-primary-500 bg-primary-50/50'
                    : 'border-border-subtle bg-slate-50/50 hover:bg-slate-50 hover:border-slate-300',
                displayError ? 'border-rose-400 bg-rose-50/20' : '',
            ]"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="onDrop"
            @click="fileInput?.click()"
        >
            <input
                ref="fileInput"
                type="file"
                :accept="accept"
                class="hidden"
                @change="onChange"
            />

            <div class="h-10 w-10 mb-2 rounded-full bg-primary-100 flex items-center justify-center text-primary-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>

            <p class="text-sm font-medium text-slate-700">
                <span class="text-primary-600 hover:underline">Klik untuk unggah</span> atau seret file ke sini
            </p>
            <p class="mt-1 text-xs text-slate-400">
                {{ helperText }}
            </p>
        </div>

        <p v-if="displayError" class="text-xs text-rose-600 font-medium">
            {{ displayError }}
        </p>
    </div>
</template>
