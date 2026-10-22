<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    title?: string;
    subtitle?: string;
    padding?: 'none' | 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
    padding: 'md',
});

const paddingClasses = computed(() => {
    switch (props.padding) {
        case 'none':
            return 'p-0';
        case 'sm':
            return 'p-4';
        case 'lg':
            return 'p-8';
        case 'md':
        default:
            return 'p-6';
    }
});
</script>

<template>
    <div class="rounded-xl border border-border-subtle bg-white shadow-2xs overflow-hidden transition-shadow">
        <!-- Header -->
        <div
            v-if="title || subtitle || $slots.header || $slots.actions"
            class="flex items-center justify-between border-b border-border-subtle px-6 py-4"
        >
            <slot name="header">
                <div>
                    <h3 v-if="title" class="text-base font-semibold text-slate-800">
                        {{ title }}
                    </h3>
                    <p v-if="subtitle" class="mt-0.5 text-xs text-slate-500">
                        {{ subtitle }}
                    </p>
                </div>
            </slot>

            <div v-if="$slots.actions" class="flex items-center gap-2">
                <slot name="actions" />
            </div>
        </div>

        <!-- Body -->
        <div :class="paddingClasses">
            <slot />
        </div>

        <!-- Footer -->
        <div
            v-if="$slots.footer"
            class="border-t border-border-subtle bg-slate-50/60 px-6 py-3"
        >
            <slot name="footer" />
        </div>
    </div>
</template>
