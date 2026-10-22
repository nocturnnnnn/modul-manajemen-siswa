<script setup lang="ts">
interface Props {
    modelValue?: string | null;
    id?: string;
    label?: string;
    rows?: number;
    placeholder?: string;
    error?: string;
    required?: boolean;
    disabled?: boolean;
}

withDefaults(defineProps<Props>(), {
    id: () => `textarea-${Math.random().toString(36).substring(2, 9)}`,
    rows: 3,
    required: false,
    disabled: false,
    placeholder: '',
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

        <textarea
            :id="id"
            :rows="rows"
            :value="modelValue ?? ''"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            class="block w-full rounded-lg border bg-white p-3 text-sm text-slate-900 shadow-2xs transition-colors placeholder:text-slate-400 focus:outline-hidden disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed"
            :class="[
                error
                    ? 'border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-200'
                    : 'border-border-subtle focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20',
            ]"
            @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        />

        <p v-if="error" class="text-xs text-rose-600 font-medium">
            {{ error }}
        </p>
    </div>
</template>
