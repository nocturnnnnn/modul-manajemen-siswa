<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import type { User } from '../../types';
import schoolLogo from '../assets/1000054749.png';

defineProps<{
    isOpen?: boolean;
}>();

defineEmits<{
    (e: 'close'): void;
}>();

const page = usePage();
const currentUrl = computed(() => page.url);
const { confirmAction } = useConfirmModal();

function handleLogout() {
    confirmAction({
        title: 'Keluar dari Sistem',
        message: 'Apakah Anda yakin ingin mengakhiri sesi kerja saat ini?',
        confirmText: 'Ya, Keluar',
        isDanger: true,
        onConfirm: () => {
            router.post('/logout');
        },
    });
}

interface NavItem {
    label: string;
    href: string;
    icon: string;
    activePrefix: string;
}

const navItems: NavItem[] = [
    { label: 'Dashboard', href: '/', icon: 'dashboard', activePrefix: '/' },
    {
        label: 'Data Siswa',
        href: '/siswa',
        icon: 'users',
        activePrefix: '/siswa',
    },
    {
        label: 'Dokumen Digital',
        href: '/dokumen',
        icon: 'document',
        activePrefix: '/dokumen',
    },
    {
        label: 'Kenaikan Kelas',
        href: '/akademik/kenaikan-kelas',
        icon: 'academic',
        activePrefix: '/akademik',
    },
    {
        label: 'Mutasi & Pindah',
        href: '/mutasi',
        icon: 'switch',
        activePrefix: '/mutasi',
    },
    {
        label: 'Kelulusan',
        href: '/kelulusan',
        icon: 'badge',
        activePrefix: '/kelulusan',
    },
    {
        label: 'Portal Alumni',
        href: '/alumni',
        icon: 'sparkles',
        activePrefix: '/alumni',
    },
    {
        label: 'Cetak Laporan',
        href: '/laporan',
        icon: 'report',
        activePrefix: '/laporan',
    },
];

const masterItems: NavItem[] = [
    {
        label: 'Kelas & Rombel',
        href: '/master/kelas',
        icon: 'layer',
        activePrefix: '/master/kelas',
    },
    {
        label: 'Tahun Ajaran',
        href: '/master/tahun-ajaran',
        icon: 'calendar',
        activePrefix: '/master/tahun-ajaran',
    },
    {
        label: 'Agama',
        href: '/master/agama',
        icon: 'book',
        activePrefix: '/master/agama',
    },
    {
        label: 'Jenis Dokumen',
        href: '/master/jenis-dokumen',
        icon: 'file',
        activePrefix: '/master/jenis-dokumen',
    },
];

const currentUser = computed(() => page.props.auth?.user as User | undefined);
const isSuperadmin = computed(() => currentUser.value?.role === 'superadmin');

const adminItems: NavItem[] = [
    {
        label: 'Kelola Pengguna',
        href: '/admin/users',
        icon: 'users',
        activePrefix: '/admin/users',
    },
];

function isActive(item: NavItem) {
    if (item.activePrefix === '/') {
        return currentUrl.value === '/';
    }
    return currentUrl.value.startsWith(item.activePrefix);
}

const STORAGE_KEY = 'siakad_sidebar_groups';

const openGroups = ref({
    menuUtama: true,
    masterData: true,
    administrasi: true,
});

try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
        const parsed = JSON.parse(saved);
        if (typeof parsed === 'object' && parsed !== null) {
            openGroups.value.menuUtama = parsed.menuUtama ?? true;
            openGroups.value.masterData = parsed.masterData ?? true;
            openGroups.value.administrasi = parsed.administrasi ?? true;
        }
    }
} catch {
    // ignore
}

const hasActiveMenu = computed(() => navItems.some((item) => isActive(item)));
const hasActiveMaster = computed(() =>
    masterItems.some((item) => isActive(item)),
);
const hasActiveAdmin = computed(() =>
    adminItems.some((item) => isActive(item)),
);

watch(
    currentUrl,
    () => {
        if (hasActiveMenu.value) openGroups.value.menuUtama = true;
        if (hasActiveMaster.value) openGroups.value.masterData = true;
        if (hasActiveAdmin.value) openGroups.value.administrasi = true;
    },
    { immediate: true },
);

function toggleGroup(group: 'menuUtama' | 'masterData' | 'administrasi') {
    openGroups.value[group] = !openGroups.value[group];
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(openGroups.value));
    } catch {
        // ignore
    }
}
</script>

<template>
    <!-- Mobile Backdrop -->
    <div
        v-if="isOpen"
        class="backdrop-blur-2xs fixed inset-0 z-40 bg-slate-900/60 lg:hidden"
        @click="$emit('close')"
    />

    <!-- Sidebar Container -->
    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-primary-800 bg-primary-900 text-white transition-transform duration-300 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:translate-x-0"
        :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Brand Header -->
        <div
            class="flex h-16 shrink-0 items-center gap-3 border-b border-primary-800/80 px-5"
        >
            <img
                :src="schoolLogo"
                alt="Logo SMP Kristen 1 Wonosobo"
                class="h-10 w-10 shrink-0 object-contain drop-shadow-sm"
            />
            <div class="truncate">
                <h2
                    class="text-sm font-bold tracking-wide text-white uppercase"
                >
                    SMP Kristen 1
                </h2>
                <p class="text-2xs tracking-wider text-secondary-200">
                    Manajemen Siswa Wonosobo
                </p>
            </div>
        </div>

        <!-- Navigation Links -->
        <div
            class="flex-1 space-y-6 overflow-y-auto overscroll-contain px-3.5 py-4"
        >
            <!-- Menu Utama -->
            <div class="space-y-1">
                <button
                    type="button"
                    class="group text-2xs flex w-full cursor-pointer items-center justify-between rounded-lg px-2.5 py-1.5 font-semibold tracking-wider text-primary-300/80 uppercase transition-colors hover:bg-primary-800/40 hover:text-white"
                    @click="toggleGroup('menuUtama')"
                >
                    <span class="flex items-center gap-1.5">
                        <span>Menu Utama</span>
                        <span
                            v-if="!openGroups.menuUtama && hasActiveMenu"
                            class="h-1.5 w-1.5 rounded-full bg-secondary-400"
                        />
                    </span>
                    <svg
                        class="h-3.5 w-3.5 text-primary-400 transition-transform duration-200 group-hover:text-white"
                        :class="
                            openGroups.menuUtama ? 'rotate-0' : '-rotate-90'
                        "
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>

                <div v-show="openGroups.menuUtama" class="space-y-1 pt-0.5">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                        :class="[
                            isActive(item)
                                ? 'bg-primary-800 font-semibold text-white shadow-xs'
                                : 'text-primary-100/80 hover:bg-primary-800/60 hover:text-white',
                        ]"
                    >
                        <span
                            class="h-2 w-2 rounded-full transition-colors"
                            :class="
                                isActive(item)
                                    ? 'bg-secondary-400'
                                    : 'bg-transparent group-hover:bg-primary-400/50'
                            "
                        />
                        <span>{{ item.label }}</span>
                    </Link>
                </div>
            </div>

            <!-- Master Data Referensi -->
            <div class="space-y-1">
                <button
                    type="button"
                    class="group text-2xs flex w-full cursor-pointer items-center justify-between rounded-lg px-2.5 py-1.5 font-semibold tracking-wider text-primary-300/80 uppercase transition-colors hover:bg-primary-800/40 hover:text-white"
                    @click="toggleGroup('masterData')"
                >
                    <span class="flex items-center gap-1.5">
                        <span>Master Data</span>
                        <span
                            v-if="!openGroups.masterData && hasActiveMaster"
                            class="h-1.5 w-1.5 rounded-full bg-secondary-400"
                        />
                    </span>
                    <svg
                        class="h-3.5 w-3.5 text-primary-400 transition-transform duration-200 group-hover:text-white"
                        :class="
                            openGroups.masterData ? 'rotate-0' : '-rotate-90'
                        "
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>

                <div v-show="openGroups.masterData" class="space-y-1 pt-0.5">
                    <Link
                        v-for="item in masterItems"
                        :key="item.href"
                        :href="item.href"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                        :class="[
                            isActive(item)
                                ? 'bg-primary-800 font-semibold text-white shadow-xs'
                                : 'text-primary-100/80 hover:bg-primary-800/60 hover:text-white',
                        ]"
                    >
                        <span
                            class="h-2 w-2 rounded-full transition-colors"
                            :class="
                                isActive(item)
                                    ? 'bg-secondary-400'
                                    : 'bg-transparent group-hover:bg-primary-400/50'
                            "
                        />
                        <span>{{ item.label }}</span>
                    </Link>
                </div>
            </div>

            <!-- Administrasi Sistem (Khusus Superadmin) -->
            <div v-if="isSuperadmin" class="space-y-1">
                <button
                    type="button"
                    class="group text-2xs flex w-full cursor-pointer items-center justify-between rounded-lg px-2.5 py-1.5 font-semibold tracking-wider text-primary-300/80 uppercase transition-colors hover:bg-primary-800/40 hover:text-white"
                    @click="toggleGroup('administrasi')"
                >
                    <span class="flex items-center gap-1.5">
                        <span>Administrasi Sistem</span>
                        <span
                            v-if="!openGroups.administrasi && hasActiveAdmin"
                            class="h-1.5 w-1.5 rounded-full bg-secondary-400"
                        />
                    </span>
                    <svg
                        class="h-3.5 w-3.5 text-primary-400 transition-transform duration-200 group-hover:text-white"
                        :class="
                            openGroups.administrasi ? 'rotate-0' : '-rotate-90'
                        "
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>

                <div v-show="openGroups.administrasi" class="space-y-1 pt-0.5">
                    <Link
                        v-for="item in adminItems"
                        :key="item.href"
                        :href="item.href"
                        class="group flex items-center gap-3 rounded-lg px-3 py-2 text-xs font-medium transition-colors"
                        :class="[
                            isActive(item)
                                ? 'bg-primary-800 font-semibold text-white shadow-xs'
                                : 'text-primary-100/80 hover:bg-primary-800/60 hover:text-white',
                        ]"
                    >
                        <span
                            class="h-2 w-2 rounded-full transition-colors"
                            :class="
                                isActive(item)
                                    ? 'bg-secondary-400'
                                    : 'bg-transparent group-hover:bg-primary-400/50'
                            "
                        />
                        <span>{{ item.label }}</span>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer / Versi & Logout -->
        <div
            class="shrink-0 space-y-2 border-t border-primary-800/80 bg-primary-950/40 p-3"
        >
            <button
                type="button"
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-primary-900/80 px-3 py-2 text-xs font-medium text-primary-200 transition-colors hover:bg-rose-950/60 hover:text-rose-200"
                @click="handleLogout"
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
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                </svg>
                <span>Keluar Sistem</span>
            </button>
            <p class="text-2xs text-center text-primary-400">
                SIAKAD Siswa • v1.0.0
            </p>
        </div>
    </aside>
</template>
