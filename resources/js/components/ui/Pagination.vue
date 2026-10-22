<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { PaginationLink } from '../../types';

interface Props {
    links: PaginationLink[];
    from?: number | null;
    to?: number | null;
    total?: number;
}

defineProps<Props>();
</script>

<template>
    <div
        v-if="links && links.length > 3"
        class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3"
    >
        <!-- Meta info -->
        <p v-if="total !== undefined" class="text-xs text-slate-500">
            Menampilkan
            <span class="font-medium text-slate-700">{{ from ?? 0 }}</span>
            sampai
            <span class="font-medium text-slate-700">{{ to ?? 0 }}</span>
            dari
            <span class="font-medium text-slate-700">{{ total }}</span>
            data
        </p>
        <div v-else />

        <!-- Links buttons -->
        <nav class="inline-flex items-center -space-x-px rounded-lg border border-border-subtle bg-white shadow-2xs overflow-hidden">
            <template v-for="(link, key) in links" :key="key">
                <span
                    v-if="!link.url"
                    class="px-3 py-1.5 text-xs text-slate-400 select-none bg-slate-50 border-r border-border-subtle last:border-r-0"
                    v-html="link.label"
                />

                <Link
                    v-else
                    :href="link.url"
                    preserve-scroll
                    preserve-state
                    class="px-3 py-1.5 text-xs border-r border-border-subtle last:border-r-0 transition-colors"
                    :class="[
                        link.active
                            ? 'bg-primary-600 text-white font-semibold'
                            : 'text-slate-600 hover:bg-primary-50/70 hover:text-primary-700',
                    ]"
                    v-html="link.label"
                />
            </template>
        </nav>
    </div>
</template>
