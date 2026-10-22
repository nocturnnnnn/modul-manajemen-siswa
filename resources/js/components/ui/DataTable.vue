<script setup lang="ts">
export interface TableHeader {
    key: string;
    label: string;
    align?: 'left' | 'center' | 'right';
    width?: string;
}

interface Props {
    headers: TableHeader[];
    items: unknown[];
    loading?: boolean;
    emptyMessage?: string;
}

withDefaults(defineProps<Props>(), {
    loading: false,
    emptyMessage: 'Tidak ada data yang ditemukan.',
});
</script>

<template>
    <div class="w-full overflow-hidden rounded-xl border border-border-subtle bg-white shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border-subtle text-left text-sm">
                <!-- Table Header -->
                <thead class="bg-surface-subtle text-slate-700 font-semibold text-xs uppercase tracking-wider">
                    <tr>
                        <th
                            v-for="header in headers"
                            :key="header.key"
                            scope="col"
                            class="px-4 py-3.5"
                            :class="[
                                header.align === 'center'
                                    ? 'text-center'
                                    : header.align === 'right'
                                      ? 'text-right'
                                      : 'text-left',
                                header.width ?? '',
                            ]"
                        >
                            {{ header.label }}
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="divide-y divide-border-subtle bg-white">
                    <!-- Loading state -->
                    <tr v-if="loading">
                        <td :colspan="headers.length" class="py-12 text-center text-slate-500">
                            <div class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memuat data...</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Empty state -->
                    <tr v-else-if="items.length === 0">
                        <td :colspan="headers.length" class="py-12 text-center text-slate-400">
                            <slot name="empty">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-sm">{{ emptyMessage }}</p>
                                </div>
                            </slot>
                        </td>
                    </tr>

                    <!-- Rows -->
                    <template v-else>
                        <slot :items="items" />
                    </template>
                </tbody>
            </table>
        </div>

        <div v-if="$slots.footer" class="border-t border-border-subtle bg-slate-50/60 px-4 py-3">
            <slot name="footer" />
        </div>
    </div>
</template>
