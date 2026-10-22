<script setup lang="ts">
import Breadcrumbs from './Breadcrumbs.vue';
import type { BreadcrumbItem } from '../../types';

defineProps<{
    title: string;
    subtitle?: string;
    breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
    <div
        class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="space-y-1">
            <slot name="breadcrumbs">
                <Breadcrumbs v-if="breadcrumbs" :items="breadcrumbs" />
            </slot>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                {{ title }}
            </h1>

            <p v-if="subtitle" class="text-xs text-slate-500">
                {{ subtitle }}
            </p>
        </div>

        <div v-if="$slots.actions" class="flex flex-wrap items-center gap-2.5">
            <slot name="actions" />
        </div>
    </div>
</template>
