<script setup lang="ts">
import { computed, watch, onMounted, onUnmounted } from 'vue';

interface Props {
    show: boolean;
    title?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
    closeable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    closeable: true,
});

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const maxWidthClass = computed(() => {
    switch (props.size) {
        case 'sm':
            return 'max-w-sm';
        case 'lg':
            return 'max-w-2xl';
        case 'xl':
            return 'max-w-4xl';
        case '2xl':
            return 'max-w-6xl';
        case 'md':
        default:
            return 'max-w-lg';
    }
});

function close() {
    if (props.closeable) {
        emit('close');
    }
}

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape' && props.show) {
        close();
    }
}

watch(
    () => props.show,
    (val) => {
        if (val) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
);

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-2xs p-4 sm:p-6 flex items-center justify-center"
                @click.self="close"
            >
                <div
                    class="relative w-full rounded-2xl bg-white shadow-xl transition-all border border-border-subtle overflow-hidden"
                    :class="maxWidthClass"
                >
                    <!-- Header -->
                    <div
                        v-if="title || $slots.header"
                        class="flex items-center justify-between border-b border-border-subtle px-6 py-4 bg-slate-50/50"
                    >
                        <slot name="header">
                            <h3 class="text-base font-semibold text-slate-800">
                                {{ title }}
                            </h3>
                        </slot>

                        <button
                            v-if="closeable"
                            type="button"
                            class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors cursor-pointer"
                            @click="close"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5 max-h-[75vh] overflow-y-auto">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div
                        v-if="$slots.footer"
                        class="border-t border-border-subtle bg-slate-50/60 px-6 py-3.5 flex items-center justify-end gap-3"
                    >
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
