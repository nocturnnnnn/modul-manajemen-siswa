<script setup lang="ts">
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '../../../components/ui/Modal.vue';
import Input from '../../../components/ui/Input.vue';
import Select from '../../../components/ui/Select.vue';
import Button from '../../../components/ui/Button.vue';
import type { User, UserRole, SelectOption } from '../../../types';

interface Props {
    show: boolean;
    user?: User | null;
}

const props = withDefaults(defineProps<Props>(), {
    user: null,
});

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const isEdit = computed(() => !!props.user);

const roleOptions: SelectOption[] = [
    { value: 'admin', label: 'Admin Tata Usaha' },
    { value: 'superadmin', label: 'Superadministrator' },
];

const form = useForm({
    name: '',
    username: '',
    role: 'admin' as UserRole,
    email: '',
    password: '',
    password_confirmation: '',
});

watch(
    () => props.user,
    (newUser) => {
        if (newUser) {
            form.name = newUser.name;
            form.username = newUser.username;
            form.role = newUser.role;
            form.email = newUser.email;
            form.password = '';
            form.password_confirmation = '';
        } else {
            form.reset();
            form.role = 'admin';
        }
        form.clearErrors();
    },
    { immediate: true },
);

function submit() {
    if (isEdit.value && props.user) {
        form.put(`/admin/users/${props.user.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
                form.reset();
            },
        });
    } else {
        form.post('/admin/users', {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
                form.reset();
            },
        });
    }
}
</script>

<template>
    <Modal
        :show="show"
        :title="isEdit ? 'Ubah Data Pengguna' : 'Tambah Pengguna Baru'"
        size="md"
        @close="emit('close')"
    >
        <form @submit.prevent="submit" class="space-y-4">
            <Input
                v-model="form.name"
                label="Nama Lengkap"
                placeholder="cth: Yohanes Pratama"
                :error="form.errors.name"
                required
            />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <Input
                    v-model="form.username"
                    label="Username"
                    placeholder="cth: yohanes"
                    :error="form.errors.username"
                    required
                />

                <Select
                    v-model="form.role"
                    label="Peran Akun"
                    :options="roleOptions"
                    :error="form.errors.role"
                    required
                />
            </div>

            <Input
                v-model="form.email"
                type="email"
                label="Alamat Email"
                placeholder="nama@sekolah.sch.id"
                :error="form.errors.email"
                required
            />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <Input
                    v-model="form.password"
                    type="password"
                    :label="
                        isEdit ? 'Kata Sandi Baru (Opsional)' : 'Kata Sandi'
                    "
                    :placeholder="
                        isEdit ? 'Kosongkan jika tak diubah' : 'Min. 8 karakter'
                    "
                    :error="form.errors.password"
                    :required="!isEdit"
                    autocomplete="new-password"
                />

                <Input
                    v-model="form.password_confirmation"
                    type="password"
                    label="Konfirmasi Sandi"
                    placeholder="Ulangi sandi"
                    :error="form.errors.password_confirmation"
                    :required="!isEdit || !!form.password"
                    autocomplete="new-password"
                />
            </div>

            <div
                class="flex items-center justify-end gap-2.5 border-t border-border-subtle pt-4"
            >
                <Button
                    type="button"
                    variant="outline"
                    @click="emit('close')"
                    :disabled="form.processing"
                >
                    Batal
                </Button>
                <Button
                    type="submit"
                    variant="primary"
                    :loading="form.processing"
                    :disabled="form.processing"
                >
                    {{ isEdit ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                </Button>
            </div>
        </form>
    </Modal>
</template>
