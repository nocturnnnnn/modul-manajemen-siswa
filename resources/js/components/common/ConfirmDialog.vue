<script setup lang="ts">
import Modal from '../ui/Modal.vue';
import Button from '../ui/Button.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';

const {
    isOpen,
    isLoading,
    title,
    message,
    confirmText,
    cancelText,
    isDanger,
    handleConfirm,
    handleCancel,
} = useConfirmModal();
</script>

<template>
    <Modal :show="isOpen" :title="title" size="sm" @close="handleCancel">
        <div class="py-2">
            <div class="flex items-start gap-3.5">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                    :class="
                        isDanger
                            ? 'bg-rose-100 text-rose-600'
                            : 'bg-primary-100 text-primary-600'
                    "
                >
                    <svg
                        v-if="isDanger"
                        class="h-5 w-5"
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
                        class="h-5 w-5"
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

                <div class="pt-1 text-sm leading-relaxed text-slate-600">
                    {{ message }}
                </div>
            </div>
        </div>

        <template #footer>
            <Button
                variant="outline"
                size="sm"
                :disabled="isLoading"
                @click="handleCancel"
            >
                {{ cancelText }}
            </Button>

            <Button
                :variant="isDanger ? 'danger' : 'primary'"
                size="sm"
                :loading="isLoading"
                @click="handleConfirm"
            >
                {{ confirmText }}
            </Button>
        </template>
    </Modal>
</template>
