<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Card from '../../../components/ui/Card.vue';
import Input from '../../../components/ui/Input.vue';
import Button from '../../../components/ui/Button.vue';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

function updatePassword() {
    form.put('/profile/password', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
            }
            if (form.errors.current_password) {
                form.reset('current_password');
            }
        },
    });
}
</script>

<template>
    <Card
        title="Ubah Kata Sandi"
        subtitle="Pastikan akun Anda menggunakan kata sandi yang kuat dan aman minimal 8 karakter."
    >
        <form @submit.prevent="updatePassword" class="space-y-4">
            <!-- Kata Sandi Saat Ini -->
            <Input
                v-model="form.current_password"
                type="password"
                label="Kata Sandi Saat Ini"
                placeholder="Masukkan kata sandi aktif"
                :error="form.errors.current_password"
                autocomplete="current-password"
                required
            />

            <!-- Kata Sandi Baru -->
            <Input
                v-model="form.password"
                type="password"
                label="Kata Sandi Baru"
                placeholder="Minimal 8 karakter"
                :error="form.errors.password"
                autocomplete="new-password"
                required
            />

            <!-- Konfirmasi Kata Sandi Baru -->
            <Input
                v-model="form.password_confirmation"
                type="password"
                label="Konfirmasi Kata Sandi Baru"
                placeholder="Ulangi kata sandi baru"
                :error="form.errors.password_confirmation"
                autocomplete="new-password"
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
                        ✓ Kata sandi berhasil diperbarui.
                    </p>
                </transition>

                <div class="ml-auto">
                    <Button
                        type="submit"
                        variant="primary"
                        :loading="form.processing"
                        :disabled="form.processing"
                    >
                        Perbarui Kata Sandi
                    </Button>
                </div>
            </div>
        </form>
    </Card>
</template>
