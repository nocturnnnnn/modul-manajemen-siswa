<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import Sidebar from './Sidebar.vue';
import Navbar from './Navbar.vue';
import ToastContainer from './ToastContainer.vue';
import ConfirmDialog from './ConfirmDialog.vue';
import { useNotification } from '../../composables/useNotification';

defineProps<{
    title?: string;
}>();

const isSidebarOpen = ref(false);
const page = usePage();
const { success, error, warning, info } = useNotification();

// Flash message listener from Laravel Session
watch(
    () => page.props.flash as Record<string, string> | undefined,
    (flash) => {
        if (!flash) return;
        if (flash.success) success(flash.success);
        if (flash.error) error(flash.error);
        if (flash.warning) warning(flash.warning);
        if (flash.info) info(flash.info);
    },
    { immediate: true, deep: true },
);
</script>

<template>
    <div class="flex min-h-screen bg-surface-light text-slate-800">
        <Head
            :title="
                title
                    ? `${title} - SMP Kristen 1 Wonosobo`
                    : 'SMP Kristen 1 Wonosobo'
            "
        />

        <!-- Sidebar Navigation -->
        <Sidebar :is-open="isSidebarOpen" @close="isSidebarOpen = false" />

        <!-- Main Content Area -->
        <div class="flex min-w-0 flex-1 flex-col">
            <!-- Navbar -->
            <Navbar @toggle-sidebar="isSidebarOpen = !isSidebarOpen" />

            <!-- Page Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </main>
        </div>

        <!-- Floating Dialogs & Notifications -->
        <ToastContainer />
        <ConfirmDialog />
    </div>
</template>
