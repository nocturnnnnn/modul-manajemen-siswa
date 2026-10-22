import { ref } from 'vue';
import type { NotificationToast } from '../types';

const notifications = ref<NotificationToast[]>([]);

export function useNotification() {
    function addNotification(notification: Omit<NotificationToast, 'id'>) {
        const id = Math.random().toString(36).substring(2, 9);
        const newToast: NotificationToast = {
            id,
            timeout: 4000,
            ...notification,
        };

        notifications.value.push(newToast);

        if (newToast.timeout && newToast.timeout > 0) {
            setTimeout(() => {
                removeNotification(id);
            }, newToast.timeout);
        }

        return id;
    }

    function removeNotification(id: string) {
        notifications.value = notifications.value.filter((n) => n.id !== id);
    }

    function success(message: string, title: string = 'Berhasil') {
        return addNotification({ type: 'success', message, title });
    }

    function error(message: string, title: string = 'Terjadi Kesalahan') {
        return addNotification({
            type: 'error',
            message,
            title,
            timeout: 6000,
        });
    }

    function warning(message: string, title: string = 'Peringatan') {
        return addNotification({
            type: 'warning',
            message,
            title,
            timeout: 5000,
        });
    }

    function info(message: string, title: string = 'Informasi') {
        return addNotification({ type: 'info', message, title });
    }

    return {
        notifications,
        addNotification,
        removeNotification,
        success,
        error,
        warning,
        info,
    };
}
