<script setup lang="ts">
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Badge from '../ui/Badge.vue';
import type { TahunAjaran, User } from '../../types';
import { useConfirmModal } from '../../composables/useConfirmModal';

defineEmits<{
    (e: 'toggleSidebar'): void;
}>();

const page = usePage();
const { confirmAction } = useConfirmModal();

const user = computed(() => {
    return (
        (page.props.auth?.user as User | undefined) ?? {
            id: 1,
            name: 'Admin Tata Usaha',
            username: 'admin',
            role: 'admin',
            email: 'admin@smpkristen1wonosobo.sch.id',
            created_at: '',
            updated_at: '',
            email_verified_at: null,
        }
    );
});

const activeYear = computed(
    () => page.props.tahunAjaranAktif as TahunAjaran | null | undefined,
);

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
</script>

<template>
    <header
        class="sticky top-0 z-30 flex h-16 shrink-0 items-center justify-between border-b border-border-subtle bg-white/95 px-4 backdrop-blur-xs sm:px-6"
    >
        <!-- Left: Mobile Toggle & Context -->
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="cursor-pointer rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
                @click="$emit('toggleSidebar')"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>

            <!-- Active Academic Year Badge -->
            <div class="hidden items-center gap-2 sm:flex">
                <span class="text-xs font-medium text-slate-500"
                    >Tahun Ajaran:</span
                >
                <Badge variant="primary" size="sm" dot>
                    {{
                        activeYear
                            ? `${activeYear.nm_tahun} • ${activeYear.semester}`
                            : '2026/2027 • Ganjil'
                    }}
                </Badge>
            </div>
        </div>

        <!-- Right: Profile & School Info -->
        <div class="flex items-center gap-3.5">
            <!-- User Info Link to Profile -->
            <Link
                href="/profile"
                class="flex items-center gap-2.5 rounded-lg p-1 text-right transition-colors hover:bg-slate-50 focus:ring-2 focus:ring-primary-300 focus:outline-hidden"
                title="Buka Pengaturan Profil"
            >
                <div class="hidden md:block">
                    <p class="text-xs font-semibold text-slate-800">
                        {{ user.name }}
                    </p>
                    <p class="text-2xs text-slate-400">
                        @{{ user.username }} •
                        <span
                            :class="
                                user.role === 'superadmin'
                                    ? 'font-semibold text-primary-600'
                                    : 'text-slate-500'
                            "
                        >
                            {{
                                user.role === 'superadmin'
                                    ? 'Superadmin'
                                    : 'Tata Usaha'
                            }}
                        </span>
                    </p>
                </div>

                <div
                    class="flex h-9 w-9 items-center justify-center rounded-full border border-primary-200 bg-primary-100 text-xs font-bold text-primary-700 shadow-2xs"
                >
                    {{ user.name.charAt(0) }}
                </div>
            </Link>

            <!-- Logout Button -->
            <button
                type="button"
                title="Keluar dari Sistem"
                class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-lg border border-border-subtle text-slate-500 transition-colors hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600"
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
            </button>
        </div>
    </header>
</template>
