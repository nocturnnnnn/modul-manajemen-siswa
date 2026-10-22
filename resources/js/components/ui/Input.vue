<script setup lang="ts">
interface Props {
    modelValue?: string | number | null;
    id?: string;
    label?: string;
    type?: string;
    placeholder?: string;
    error?: string;
    hint?: string;
    required?: boolean;
    disabled?: boolean;
    autocomplete?: string;
}

withDefaults(defineProps<Props>(), {
    id: () => `input-${Math.random().toString(36).substring(2, 9)}`,
    type: 'text',
    required: false,
    disabled: false,
    placeholder: '',
    error: '',
    hint: '',
    autocomplete: 'off',
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
            <div
                v-if="$slots.leading"
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
            >
                <slot name="leading" />
            </div>

            <input
                :id="id"
                :type="type"
                :value="modelValue ?? ''"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
                :autocomplete="autocomplete"
                class="block w-full rounded-lg border bg-white py-2 text-sm text-slate-900 transition-colors placeholder:text-slate-400 focus:outline-hidden disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed"
                :class="[
                    $slots.leading ? 'pl-10' : 'pl-3.5',
                    $slots.trailing ? 'pr-10' : 'pr-3.5',
                    error
                        ? 'border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-200'
                        : 'border-border-subtle focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20',
                ]"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
            />

            <div
                v-if="$slots.trailing"
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400"
            >
                <slot name="trailing" />
            </div>
        </div>

        <p v-if="error" class="text-xs text-rose-600 font-medium">
            {{ error }}
        </p>
        <p v-else-if="hint" class="text-xs text-slate-500">
            {{ hint }}
        </p>
    </div>
</template>
