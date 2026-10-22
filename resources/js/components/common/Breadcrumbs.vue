<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '../../types';

defineProps<{
    items?: BreadcrumbItem[];
}>();
</script>

<template>
    <nav v-if="items && items.length > 0" class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1.5 text-xs md:space-x-2">
            <!-- Home -->
            <li class="inline-flex items-center">
                <Link
                    href="/"
                    class="inline-flex items-center text-slate-500 transition-colors hover:text-primary-700"
                >
                    <svg
                        class="mr-1 h-3.5 w-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        />
                    </svg>
                    <span>Dashboard</span>
                </Link>
            </li>

            <!-- Separator & Items -->
            <li v-for="(item, index) in items" :key="index">
                <div class="flex items-center">
                    <svg
                        class="mx-1 h-3.5 w-3.5 text-slate-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5l7 7-7 7"
                        />
                    </svg>

                    <Link
                        v-if="item.href && !item.current"
                        :href="item.href"
                        class="font-medium text-slate-500 transition-colors hover:text-primary-700"
                    >
                        {{ item.label }}
                    </Link>
                    <span
                        v-else
                        class="font-semibold text-slate-800"
                        aria-current="page"
                    >
                        {{ item.label }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>
