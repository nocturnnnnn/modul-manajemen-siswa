<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'primary' | 'secondary' | 'danger' | 'outline' | 'ghost';
    size?: 'sm' | 'md' | 'lg';
    type?: 'button' | 'submit' | 'reset';
    disabled?: boolean;
    loading?: boolean;
    fullWidth?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'primary',
    size: 'md',
    type: 'button',
    disabled: false,
    loading: false,
    fullWidth: false,
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'secondary':
            return 'bg-secondary-50 text-secondary-700 border border-secondary-200 hover:bg-secondary-100 focus:ring-secondary-300';
        case 'danger':
            return 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 focus:ring-rose-300';
        case 'outline':
            return 'bg-white text-slate-700 border border-border-subtle hover:bg-surface-subtle focus:ring-primary-200';
        case 'ghost':
            return 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-200';
        case 'primary':
        default:
            return 'bg-primary-600 text-white shadow-xs hover:bg-primary-700 focus:ring-primary-300';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'px-2.5 py-1.5 text-xs';
        case 'lg':
            return 'px-5 py-2.5 text-base';
        case 'md':
        default:
            return 'px-3.5 py-2 text-sm';
    }
});
</script>

<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        class="inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-colors focus:outline-hidden focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
        :class="[variantClasses, sizeClasses, fullWidth ? 'w-full' : '']"
    >
        <svg
            v-if="loading"
            class="animate-spin -ml-0.5 h-4 w-4 shrink-0 text-current"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <slot />
    </button>
</template>
