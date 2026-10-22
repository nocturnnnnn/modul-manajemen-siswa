<script setup lang="ts">
import { computed } from 'vue';
import Badge from '../../ui/Badge.vue';
import type { StatusSiswa } from '../../../types';

const props = defineProps<{
    status: StatusSiswa;
}>();

const config = computed(() => {
    switch (props.status) {
        case 'aktif':
            return { variant: 'success' as const, label: 'Aktif' };
        case 'mutasi':
            return { variant: 'warning' as const, label: 'Mutasi' };
        case 'pindah':
            return { variant: 'danger' as const, label: 'Pindah' };
        case 'lulus':
            return { variant: 'info' as const, label: 'Lulus' };
        case 'alumni':
            return { variant: 'primary' as const, label: 'Alumni' };
        default:
            return { variant: 'neutral' as const, label: props.status };
    }
});
</script>

<template>
    <Badge :variant="config.variant" size="sm" dot>
        {{ config.label }}
    </Badge>
</template>
