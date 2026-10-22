<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Card from '../../../components/ui/Card.vue';
import Input from '../../../components/ui/Input.vue';
import Button from '../../../components/ui/Button.vue';
import Badge from '../../../components/ui/Badge.vue';
import type { User } from '../../../types';

interface Props {
    user: User;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
});

function submit() {
    form.patch('/profile', {
        preserveScroll: true,
    });
}
</script>

<template>
    <Card
        title="Informasi Profil & Akun"
        subtitle="Perbarui nama lengkap, username untuk login, serta alamat email akun Anda."
    >
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Read-only Role & ID Info -->
            <div
                class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-primary-100 bg-primary-50/50 p-3.5"
            >
                <div class="space-y-0.5">
                    <p class="text-xs font-semibold text-primary-950">
                        Peran & Hak Akses Akun
                    </p>
                    <p class="text-2xs text-slate-500">
                        Peran akun hanya dapat diubah oleh Superadmin pada menu
                        Administrasi.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Badge
                        :variant="
                            user.role === 'superadmin' ? 'primary' : 'neutral'
                        "
                        size="md"
                    >
                        {{
                            user.role === 'superadmin'
                                ? '🛡️ Superadministrator'
                                : '👤 Admin Tata Usaha'
                        }}
                    </Badge>
                </div>
            </div>

            <!-- Nama Lengkap -->
            <Input
                v-model="form.name"
                label="Nama Lengkap"
                placeholder="Masukkan nama lengkap"
                :error="form.errors.name"
                required
            />

            <!-- Username -->
            <Input
                v-model="form.username"
                label="Username Login"
                placeholder="Masukkan username unik"
                :error="form.errors.username"
                hint="Digunakan untuk masuk ke sistem. Huruf kecil, angka, tanda titik, atau underscore."
                required
            />

            <!-- Email -->
            <Input
                v-model="form.email"
                type="email"
                label="Alamat Email"
                placeholder="nama@sekolah.sch.id"
                :error="form.errors.email"
                required
            />

            <!-- Form Actions -->
            <div
                class="flex items-center justify-between border-t border-border-subtle pt-4"
            >
                <transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs font-medium text-emerald-600"
                    >
                        ✓ Profil berhasil diperbarui.
                    </p>
                </transition>

                <div class="ml-auto">
                    <Button
                        type="submit"
                        variant="primary"
                        :loading="form.processing"
                        :disabled="form.processing"
                    >
                        Simpan Perubahan
                    </Button>
                </div>
            </div>
        </form>
    </Card>
</template>
