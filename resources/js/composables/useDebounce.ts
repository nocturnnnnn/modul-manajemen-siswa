import { customRef } from 'vue';

/**
 * Debounced ref helper untuk input pencarian live.
 */
export function useDebouncedRef<T>(value: T, delay: number = 300) {
    let timeout: ReturnType<typeof setTimeout>;
    return customRef((track, trigger) => {
        return {
            get() {
                track();
                return value;
            },
            set(newValue: T) {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    value = newValue;
                    trigger();
                }, delay);
            },
        };
    });
}

/**
 * Standard debounce function wrapper.
 */
export function debounce<T extends (...args: unknown[]) => void>(
    fn: T,
    delay: number = 300,
) {
    let timeoutId: ReturnType<typeof setTimeout> | null = null;
    return (...args: Parameters<T>) => {
        if (timeoutId) clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            fn(...args);
        }, delay);
    };
}
