<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import UpdateProfileInformationCard from './partials/UpdateProfileInformationCard.vue';
import UpdatePasswordCard from './partials/UpdatePasswordCard.vue';
import type { BreadcrumbItem, User } from '../../types';

interface Props {
    user: User;
    status?: string;
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Dashboard', href: '/' },
    { label: 'Profil Akun' },
];
</script>

<template>
    <Head title="Profil Akun" />

    <AppLayout title="Profil Akun">
        <PageHeader
            title="Pengaturan Profil"
            subtitle="Kelola informasi identitas akun pribadi dan keamanan kata sandi Anda."
            :breadcrumbs="breadcrumbs"
        />

        <div class="mx-auto max-w-4xl space-y-6">
            <UpdateProfileInformationCard :user="user" />
            <UpdatePasswordCard />
        </div>
    </AppLayout>
</template>
