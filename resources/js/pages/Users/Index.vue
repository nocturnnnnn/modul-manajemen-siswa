<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import DataTable, { type TableHeader } from '../../components/ui/DataTable.vue';
import Pagination from '../../components/ui/Pagination.vue';
import Button from '../../components/ui/Button.vue';
import Input from '../../components/ui/Input.vue';
import Select from '../../components/ui/Select.vue';
import Badge from '../../components/ui/Badge.vue';
import UserModal from './partials/UserModal.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import { debounce } from '../../composables/useDebounce';
import type {
    User,
    PaginatedData,
    SelectOption,
    BreadcrumbItem,
} from '../../types';

interface Props {
    users: PaginatedData<User>;
    filters: {
        search?: string;
        role?: string;
    };
}

const props = defineProps<Props>();
const page = usePage();
const { confirmAction } = useConfirmModal();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Dashboard', href: '/' },
    { label: 'Administrasi Sistem' },
    { label: 'Kelola Pengguna', current: true },
];

const headers: TableHeader[] = [
    { key: 'no', label: 'No.', width: 'w-16' },
    { key: 'user', label: 'Nama & Email' },
    { key: 'username', label: 'Username' },
    { key: 'role', label: 'Peran Akun' },
    { key: 'created_at', label: 'Terdaftar Sejak' },
    { key: 'actions', label: 'Aksi', align: 'right', width: 'w-36' },
];

const roleOptions: SelectOption[] = [
    { value: '', label: 'Semua Peran' },
    { value: 'superadmin', label: 'Superadministrator' },
    { value: 'admin', label: 'Admin Tata Usaha' },
];

const search = ref(props.filters.search ?? '');
const role = ref(props.filters.role ?? '');
const isModalOpen = ref(false);
const editingUser = ref<User | null>(null);

const debouncedFilter = debounce(() => {
    router.get(
        '/admin/users',
        {
            search: search.value || undefined,
            role: role.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}, 300);

watch([search, role], () => debouncedFilter());

function openCreateModal() {
    editingUser.value = null;
    isModalOpen.value = true;
}

function openEditModal(user: User) {
    editingUser.value = user;
    isModalOpen.value = true;
}

function deleteUser(user: User) {
    confirmAction({
        title: 'Hapus Pengguna',
        message: `Apakah Anda yakin ingin menghapus akun "${user.name}" (@${user.username})? Tindakan ini tidak dapat dibatalkan.`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/admin/users/${user.id}`);
        },
    });
}

const currentAuthUserId = (page.props.auth?.user as User | undefined)?.id;
</script>

<template>
    <Head title="Kelola Pengguna" />

    <AppLayout title="Kelola Pengguna">
        <PageHeader
            title="Kelola Pengguna Sistem"
            subtitle="Manajemen akun Superadministrator dan Admin Tata Usaha yang memiliki akses ke sistem."
            :breadcrumbs="breadcrumbs"
        >
            <template #actions>
                <Button variant="primary" size="sm" @click="openCreateModal">
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
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    <span>Tambah Pengguna</span>
                </Button>
            </template>
        </PageHeader>

        <!-- Filters -->
        <div
            class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="w-full sm:max-w-xs">
                <Input
                    v-model="search"
                    placeholder="Cari nama, username, email..."
                />
            </div>
            <div class="w-full sm:w-56">
                <Select
                    v-model="role"
                    :options="roleOptions"
                    placeholder="Semua Peran"
                />
            </div>
        </div>

        <!-- Data Table -->
        <DataTable
            :headers="headers"
            :items="users.data"
            empty-message="Tidak ada data pengguna yang sesuai dengan filter pencarian."
        >
            <tr
                v-for="(item, idx) in users.data"
                :key="item.id"
                class="transition-colors hover:bg-primary-50/40"
            >
                <td class="px-4 py-3 text-xs text-slate-500">
                    {{ (users.from ?? 1) + idx }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-primary-200 bg-primary-100 text-xs font-bold text-primary-700"
                        >
                            {{ item.name.charAt(0) }}
                        </div>
                        <div class="truncate">
                            <p class="text-sm font-medium text-slate-800">
                                {{ item.name }}
                                <span
                                    v-if="item.id === currentAuthUserId"
                                    class="text-2xs ml-1 font-semibold text-primary-600"
                                >
                                    (Anda)
                                </span>
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ item.email }}
                            </p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 font-mono text-xs text-slate-600">
                    @{{ item.username }}
                </td>
                <td class="px-4 py-3">
                    <Badge
                        :variant="
                            item.role === 'superadmin' ? 'primary' : 'neutral'
                        "
                        size="sm"
                    >
                        {{
                            item.role === 'superadmin'
                                ? 'Superadmin'
                                : 'Tata Usaha'
                        }}
                    </Badge>
                </td>
                <td class="px-4 py-3 text-xs text-slate-500">
                    {{
                        item.created_at
                            ? new Date(item.created_at).toLocaleDateString(
                                  'id-ID',
                              )
                            : '-'
                    }}
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="inline-flex items-center gap-1.5">
                        <Button
                            variant="outline"
                            size="sm"
                            title="Ubah Pengguna"
                            @click="openEditModal(item)"
                        >
                            Edit
                        </Button>
                        <Button
                            variant="danger"
                            size="sm"
                            :title="
                                item.id === currentAuthUserId
                                    ? 'Tidak dapat menghapus akun Anda sendiri'
                                    : 'Hapus Pengguna'
                            "
                            :disabled="item.id === currentAuthUserId"
                            @click="deleteUser(item)"
                        >
                            Hapus
                        </Button>
                    </div>
                </td>
            </tr>

            <template #footer>
                <Pagination
                    :links="users.links"
                    :from="users.from"
                    :to="users.to"
                    :total="users.total"
                />
            </template>
        </DataTable>

        <UserModal
            :show="isModalOpen"
            :user="editingUser"
            @close="isModalOpen = false"
        />
    </AppLayout>
</template>
