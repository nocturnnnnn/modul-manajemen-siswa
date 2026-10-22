<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Button from '../components/ui/Button.vue';
import Badge from '../components/ui/Badge.vue';
import type { User } from '../types';
import schoolLogo from '../components/assets/1000054749.png';

interface Props {
    status?: number;
    message?: string;
}

const props = withDefaults(defineProps<Props>(), {
    status: 404,
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user as User | undefined);
const isAuthenticated = computed(() => !!currentUser.value);

interface ErrorDetails {
    title: string;
    description: string;
    badgeLabel: string;
    badgeVariant:
        | 'primary'
        | 'secondary'
        | 'warning'
        | 'danger'
        | 'info'
        | 'neutral';
}

const errorInfo = computed<ErrorDetails>(() => {
    switch (props.status) {
        case 403:
            return {
                title: 'Akses Ditolak',
                description:
                    props.message ||
                    'Maaf, Anda tidak memiliki izin atau hak akses untuk membuka halaman administratif ini.',
                badgeLabel: '403 • Forbidden',
                badgeVariant: 'warning',
            };
        case 404:
            return {
                title: 'Halaman Tidak Ditemukan',
                description:
                    props.message ||
                    'Halaman yang Anda cari tidak tersedia, telah dihapus, atau tautan yang Anda masukkan keliru.',
                badgeLabel: '404 • Not Found',
                badgeVariant: 'neutral',
            };
        case 419:
            return {
                title: 'Sesi Halaman Kedaluwarsa',
                description:
                    props.message ||
                    'Sesi kerja Anda telah berakhir karena tidak ada aktivitas. Silakan muat ulang atau masuk kembali.',
                badgeLabel: '419 • Page Expired',
                badgeVariant: 'warning',
            };
        case 500:
            return {
                title: 'Terjadi Kesalahan Server',
                description:
                    props.message ||
                    'Terjadi kendala internal pada server saat memproses permintaan Anda. Tim kami telah mencatat kendala ini.',
                badgeLabel: '500 • Internal Server Error',
                badgeVariant: 'danger',
            };
        case 503:
            return {
                title: 'Layanan Sedang Pemeliharaan',
                description:
                    props.message ||
                    'Sistem manajemen sedang dalam jadwal perawatan rutin. Silakan coba kembali dalam beberapa saat.',
                badgeLabel: '503 • Service Unavailable',
                badgeVariant: 'info',
            };
        case 505:
            return {
                title: 'Versi HTTP Tidak Didukung',
                description:
                    props.message ||
                    'Protokol versi HTTP yang dikirimkan oleh peramban Anda tidak didukung oleh server aplikasi.',
                badgeLabel: '505 • HTTP Version Not Supported',
                badgeVariant: 'danger',
            };
        default:
            return {
                title: 'Terjadi Kesalahan Sistem',
                description:
                    props.message ||
                    'Sistem mengalami kesalahan tak terduga saat memproses halaman ini.',
                badgeLabel: `${props.status} • Error`,
                badgeVariant: 'danger',
            };
    }
});

function handleGoBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = isAuthenticated.value ? '/' : '/login';
    }
}
</script>

<template>
    <Head :title="`${status} - ${errorInfo.title}`" />

    <div
        class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-surface-light px-4 py-12 sm:px-6"
    >
        <!-- Background Ambient Glow & Blue Accent Ornaments -->
        <div
            class="pointer-events-none absolute -top-40 -left-40 h-96 w-96 rounded-full bg-primary-200/40 blur-3xl"
        />
        <div
            class="pointer-events-none absolute -right-40 -bottom-40 h-96 w-96 rounded-full bg-secondary-200/40 blur-3xl"
        />

        <!-- Brand Top Header -->
        <div class="mb-6 flex items-center gap-3">
            <img
                :src="schoolLogo"
                alt="Logo SMP Kristen 1 Wonosobo"
                class="h-10 w-10 shrink-0 object-contain drop-shadow-sm"
            />
            <div>
                <h2
                    class="text-sm font-bold tracking-wide text-primary-950 uppercase"
                >
                    SMP Kristen 1 Wonosobo
                </h2>
                <p class="text-2xs font-medium text-slate-500">
                    Sistem Informasi Manajemen Siswa
                </p>
            </div>
        </div>

        <!-- Error Card -->
        <div
            class="relative z-10 w-full max-w-lg rounded-2xl border border-border-subtle bg-white/95 p-8 text-center shadow-xl backdrop-blur-xs sm:p-10"
        >
            <!-- Status Badge -->
            <div class="mb-4 flex justify-center">
                <Badge :variant="errorInfo.badgeVariant" size="md" dot>
                    {{ errorInfo.badgeLabel }}
                </Badge>
            </div>

            <!-- Big Decorative Status Code -->
            <div class="relative my-2 select-none">
                <span
                    class="bg-linear-to-br from-primary-900 via-primary-700 to-secondary-500 bg-clip-text text-7xl font-extrabold tracking-tighter text-transparent sm:text-8xl"
                >
                    {{ status }}
                </span>
            </div>

            <!-- Title & Description -->
            <h1
                class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl"
            >
                {{ errorInfo.title }}
            </h1>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">
                {{ errorInfo.description }}
            </p>

            <!-- User Status Notice -->
            <div
                v-if="isAuthenticated && currentUser"
                class="mt-5 rounded-lg border border-primary-100 bg-primary-50/60 p-3 text-xs text-primary-900"
            >
                Masuk sebagai:
                <strong class="font-semibold">{{ currentUser.name }}</strong>
                (@{{ currentUser.username }} •
                {{
                    currentUser.role === 'superadmin'
                        ? 'Superadmin'
                        : 'Tata Usaha'
                }})
            </div>

            <!-- Action Buttons -->
            <div
                class="mt-7 flex flex-col-reverse items-center justify-center gap-3 sm:flex-row"
            >
                <!-- Back Button -->
                <Button variant="outline" size="md" @click="handleGoBack">
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
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                    <span>Kembali</span>
                </Button>

                <!-- Dynamic Auth-Based Button -->
                <template v-if="isAuthenticated">
                    <Link href="/">
                        <Button variant="primary" size="md">
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
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                />
                            </svg>
                            <span>Ke Dashboard</span>
                        </Button>
                    </Link>
                </template>

                <template v-else>
                    <Link href="/login">
                        <Button variant="primary" size="md">
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
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                />
                            </svg>
                            <span>Masuk ke Sistem</span>
                        </Button>
                    </Link>
                </template>
            </div>
        </div>

        <!-- Footer Info -->
        <p class="mt-6 text-center text-xs text-slate-400">
            Butuh bantuan? Hubungi staf Tata Usaha SMP Kristen 1 Wonosobo.
        </p>
    </div>
</template>
