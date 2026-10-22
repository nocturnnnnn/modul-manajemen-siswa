<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Input from '@/components/ui/Input.vue';
import Alert from '@/components/ui/Alert.vue';
import schoolLogo from '@/components/assets/1000054749.png';

const page = usePage();
const flash = computed(
    () => page.props.flash as { info?: string; error?: string } | undefined,
);

const showPassword = ref(false);

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => {
            form.reset('password');
        },
    });
}
</script>

<template>
    <Head title="Masuk - SIAKAD SMP Kristen 1 Wonosobo" />

    <div
        class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12 sm:px-6 lg:px-8"
    >
        <div class="w-full max-w-md space-y-6">
            <!-- Brand & School Emblem -->
            <div class="text-center">
                <img
                    :src="schoolLogo"
                    alt="Logo SMP Kristen 1 Wonosobo"
                    class="mx-auto h-20 w-20 object-contain drop-shadow-md"
                />
                <h1
                    class="mt-4 text-xl font-bold tracking-tight text-primary-950 sm:text-2xl"
                >
                    SMP KRISTEN 1 WONOSOBO
                </h1>
                <p class="mt-1 text-xs font-medium text-slate-500">
                    Sistem Informasi Akademik & Manajemen Siswa
                </p>
            </div>

            <!-- Card Form Login -->
            <div
                class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs sm:p-8"
            >
                <div class="mb-5 border-b border-slate-100 pb-3">
                    <h2 class="text-base font-semibold text-slate-800">
                        Masuk ke Akun Anda
                    </h2>
                    <p class="text-xs text-slate-500">
                        Gunakan username dan kata sandi Tata Usaha yang
                        terdaftar.
                    </p>
                </div>

                <!-- Flash Info / Error Notification -->
                <div v-if="flash?.info" class="mb-5">
                    <Alert type="info" :message="flash.info" dismissible />
                </div>
                <div v-if="flash?.error" class="mb-5">
                    <Alert type="error" :message="flash.error" dismissible />
                </div>

                <form class="space-y-4" @submit.prevent="submit">
                    <!-- Field Username -->
                    <Input
                        v-model="form.username"
                        label="Username"
                        placeholder="Masukkan username"
                        :error="form.errors.username"
                        autocomplete="username"
                        required
                    >
                        <template #leading>
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
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                        </template>
                    </Input>

                    <!-- Field Password -->
                    <div class="space-y-1.5">
                        <Input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            label="Kata Sandi"
                            placeholder="Masukkan kata sandi"
                            :error="form.errors.password"
                            autocomplete="current-password"
                            required
                        >
                            <template #leading>
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
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                            </template>
                        </Input>
                        <div class="flex justify-end">
                            <button
                                type="button"
                                class="text-2xs cursor-pointer font-medium text-primary-600 transition-colors hover:text-primary-800"
                                @click="showPassword = !showPassword"
                            >
                                {{
                                    showPassword
                                        ? 'Sembunyikan kata sandi'
                                        : 'Tampilkan kata sandi'
                                }}
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Help -->
                    <div class="flex items-center justify-between pt-1">
                        <label
                            class="flex cursor-pointer items-center gap-2 select-none"
                        >
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 rounded-sm border-slate-300 text-primary-600 focus:ring-primary-500"
                            />
                            <span class="text-xs text-slate-600"
                                >Ingat sesi saya</span
                            >
                        </label>
                        <span class="text-2xs text-slate-400">v1.0.0</span>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <Button
                            type="submit"
                            variant="primary"
                            size="lg"
                            full-width
                            :loading="form.processing"
                        >
                            Masuk ke Sistem
                        </Button>
                    </div>
                </form>
            </div>

            <!-- School Footer Note -->
            <p class="text-2xs text-center text-slate-400">
                &copy; {{ new Date().getFullYear() }} SMP Kristen 1 Wonosobo.
                Hak Cipta Dilindungi.
            </p>
        </div>
    </div>
</template>
