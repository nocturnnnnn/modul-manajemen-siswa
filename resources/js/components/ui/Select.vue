<script setup lang="ts">
import type { SelectOption } from '../../types';

interface Props {
    modelValue: string | number | null;
    options: SelectOption[];
    id?: string;
    label?: string;
    placeholder?: string;
    error?: string;
    required?: boolean;
    disabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    id: () => `select-${Math.random().toString(36).substring(2, 9)}`,
    required: false,
    disabled: false,
    placeholder: '-- Pilih Opsi --',
    error: '',
});

defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
    <div class="w-full space-y-1.5">
        <label
            v-if="label"
            :for="id"
            class="block text-sm font-medium text-slate-700"
        >
            {{ label }}
            <span v-if="required" class="text-rose-500">*</span>
        </label>

        <div class="relative rounded-lg shadow-2xs">
            <select
                :id="id"
                :value="modelValue ?? ''"
                :required="required"
                :disabled="disabled"
                class="block w-full appearance-none rounded-lg border bg-white py-2 pl-3.5 pr-10 text-sm text-slate-900 transition-colors focus:outline-hidden disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed"
                :class="[
                    error
                        ? 'border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-200'
                        : 'border-border-subtle focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20',
                ]"
                @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
            >
                <option value="" disabled>{{ placeholder }}</option>
                <option
                    v-for="opt in options"
                    :key="String(opt.value)"
                    :value="opt.value"
                    :disabled="opt.disabled"
                >
                    {{ opt.label }}
                </option>
            </select>

            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>

        <p v-if="error" class="text-xs text-rose-600 font-medium">
            {{ error }}
        </p>
    </div>
</template>
