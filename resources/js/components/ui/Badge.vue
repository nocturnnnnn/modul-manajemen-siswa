<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'primary' | 'secondary' | 'success' | 'warning' | 'danger' | 'info' | 'neutral';
    size?: 'sm' | 'md';
    dot?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'neutral',
    size: 'sm',
    dot: false,
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'bg-primary-50 text-primary-700 border-primary-200';
        case 'secondary':
            return 'bg-secondary-50 text-secondary-700 border-secondary-200';
        case 'success':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'warning':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'danger':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        case 'info':
            return 'bg-sky-50 text-sky-700 border-sky-200';
        case 'neutral':
        default:
            return 'bg-slate-100 text-slate-700 border-slate-200';
    }
});

const dotColor = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'bg-primary-500';
        case 'secondary':
            return 'bg-secondary-500';
        case 'success':
            return 'bg-emerald-500';
        case 'warning':
            return 'bg-amber-500';
        case 'danger':
            return 'bg-rose-500';
        case 'info':
            return 'bg-sky-500';
        case 'neutral':
        default:
            return 'bg-slate-400';
    }
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 font-medium rounded-full border transition-colors"
        :class="[
            variantClasses,
            size === 'sm' ? 'px-2.5 py-0.5 text-xs' : 'px-3 py-1 text-sm',
        ]"
    >
        <span
            v-if="dot"
            class="h-1.5 w-1.5 rounded-full"
            :class="dotColor"
        />
        <slot />
    </span>
</template>
