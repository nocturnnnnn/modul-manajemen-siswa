<script setup lang="ts">
import { useNotification } from '../../composables/useNotification';

const { notifications, removeNotification } = useNotification();
</script>

<template>
    <div
        aria-live="assertive"
        class="pointer-events-none fixed inset-0 z-50 flex items-start justify-end p-4 sm:p-6"
    >
        <div
            class="flex w-full max-w-sm flex-col items-center space-y-3 sm:items-end"
        >
            <TransitionGroup
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-for="toast in notifications"
                    :key="toast.id"
                    class="pointer-events-auto flex w-full items-start gap-3 overflow-hidden rounded-xl border bg-white p-4 shadow-lg"
                    :class="[
                        toast.type === 'success' ? 'border-emerald-200' : '',
                        toast.type === 'error' ? 'border-rose-200' : '',
                        toast.type === 'warning' ? 'border-amber-200' : '',
                        toast.type === 'info' ? 'border-sky-200' : '',
                    ]"
                >
                    <!-- Icon -->
                    <div class="mt-0.5 shrink-0">
                        <svg
                            v-if="toast.type === 'success'"
                            class="h-5 w-5 text-emerald-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <svg
                            v-else-if="toast.type === 'error'"
                            class="h-5 w-5 text-rose-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <svg
                            v-else-if="toast.type === 'warning'"
                            class="h-5 w-5 text-amber-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-5 w-5 text-sky-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1">
                        <p
                            v-if="toast.title"
                            class="text-sm font-semibold text-slate-800"
                        >
                            {{ toast.title }}
                        </p>
                        <p
                            class="mt-0.5 text-xs leading-relaxed text-slate-600"
                        >
                            {{ toast.message }}
                        </p>
                    </div>

                    <!-- Close -->
                    <button
                        type="button"
                        class="shrink-0 cursor-pointer rounded-lg p-1 text-slate-400 transition-colors hover:text-slate-600"
                        @click="removeNotification(toast.id)"
                    >
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </div>
</template>
