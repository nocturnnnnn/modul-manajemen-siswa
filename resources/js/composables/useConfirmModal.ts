import { ref } from 'vue';

interface ConfirmOptions {
    title: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
    isDanger?: boolean;
    onConfirm: () => Promise<void> | void;
}

const isOpen = ref(false);
const isLoading = ref(false);
const title = ref('');
const message = ref('');
const confirmText = ref('Ya, Lanjutkan');
const cancelText = ref('Batal');
const isDanger = ref(false);
let resolveCallback: (() => Promise<void> | void) | null = null;

export function useConfirmModal() {
    function confirmAction(options: ConfirmOptions) {
        title.value = options.title;
        message.value = options.message;
        confirmText.value = options.confirmText ?? 'Ya, Lanjutkan';
        cancelText.value = options.cancelText ?? 'Batal';
        isDanger.value = options.isDanger ?? false;
        resolveCallback = options.onConfirm;
        isOpen.value = true;
    }

    async function handleConfirm() {
        if (!resolveCallback) return;
        try {
            isLoading.value = true;
            await resolveCallback();
            isOpen.value = false;
        } finally {
            isLoading.value = false;
            resolveCallback = null;
        }
    }

    function handleCancel() {
        isOpen.value = false;
        resolveCallback = null;
    }

    return {
        isOpen,
        isLoading,
        title,
        message,
        confirmText,
        cancelText,
        isDanger,
        confirmAction,
        handleConfirm,
        handleCancel,
    };
}
