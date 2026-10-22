<script setup lang="ts">
import { computed } from 'vue';
import Badge from '../../ui/Badge.vue';
import type { StatusVerifikasiDokumen } from '../../../types';

const props = withDefaults(
    defineProps<{
        status: StatusVerifikasiDokumen;
        size?: 'sm' | 'md';
    }>(),
    {
        size: 'sm',
    },
);

const badgeConfig = computed(() => {
    switch (props.status) {
        case 'terverifikasi':
            return {
                variant: 'success' as const,
                label: 'Terverifikasi',
                dotClass: 'bg-emerald-500',
            };
        case 'ditolak':
            return {
                variant: 'danger' as const,
                label: 'Ditolak',
                dotClass: 'bg-rose-500',
            };
        case 'belum':
        default:
            return {
                variant: 'warning' as const,
                label: 'Menunggu',
                dotClass: 'bg-amber-500',
            };
    }
});
</script>

<template>
    <Badge :variant="badgeConfig.variant" :size="size">
        <span class="inline-flex items-center gap-1.5">
            <span
                class="h-1.5 w-1.5 rounded-full"
                :class="badgeConfig.dotClass"
            />
            <span>{{ badgeConfig.label }}</span>
        </span>
    </Badge>
</template>
