<script setup lang="ts">
export interface TabItem {
    id: string;
    label: string;
    badge?: string | number;
}

interface Props {
    tabs: TabItem[];
    activeTab: string;
}

defineProps<Props>();

defineEmits<{
    (e: 'update:activeTab', id: string): void;
}>();
</script>

<template>
    <div class="border-b border-border-subtle">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                type="button"
                class="group inline-flex items-center gap-2 border-b-2 py-3 px-1 text-sm font-medium transition-colors cursor-pointer whitespace-nowrap"
                :class="[
                    activeTab === tab.id
                        ? 'border-primary-600 text-primary-700 font-semibold'
                        : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700',
                ]"
                @click="$emit('update:activeTab', tab.id)"
            >
                <slot :name="`icon-${tab.id}`" />

                <span>{{ tab.label }}</span>

                <span
                    v-if="tab.badge !== undefined"
                    class="rounded-full px-2 py-0.5 text-xs font-semibold"
                    :class="[
                        activeTab === tab.id
                            ? 'bg-primary-100 text-primary-800'
                            : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200',
                    ]"
                >
                    {{ tab.badge }}
                </span>
            </button>
        </nav>
    </div>
</template>
