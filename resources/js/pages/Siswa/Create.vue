<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Card from '../../components/ui/Card.vue';
import Button from '../../components/ui/Button.vue';
import Tabs, { type TabItem } from '../../components/ui/Tabs.vue';
import FormIdentitas from '../../components/modules/siswa/forms/FormIdentitas.vue';
import FormKeluarga from '../../components/modules/siswa/forms/FormKeluarga.vue';
import FormOrangTua from '../../components/modules/siswa/forms/FormOrangTua.vue';
import FormKontak from '../../components/modules/siswa/forms/FormKontak.vue';
import FormUploadFoto from '../../components/modules/siswa/forms/FormUploadFoto.vue';
import type { Agama } from '../../types';

defineProps<{
    agamaList: Agama[];
}>();

const activeTab = ref('identitas');

const tabs: TabItem[] = [
    { id: 'identitas', label: '1. Identitas Pokok' },
    { id: 'keluarga', label: '2. Keluarga & Masuk' },
    { id: 'orang_tua', label: '3. Orang Tua / Wali' },
    { id: 'kontak', label: '4. Kontak' },
    { id: 'foto', label: '5. Pas Foto' },
];

const form = useForm({
    nis: '',
    nisn: '',
    nik: '',
    nm_siswa: '',
    tempat_lahir: '',
    tgl_lahir: '',
    jenis_kelamin: 'Laki-laki' as 'Laki-laki' | 'Perempuan',
    kd_agama: null as number | null,
    alamat: '',
    status_siswa: 'aktif',
    keluarga: {
        no_kk: '',
        anak_ke: 1,
        jumlah_saudara: 0,
    },
    orang_tua: [
        {
            hubungan: 'ayah' as const,
            nm_ortu: '',
            nik: '',
            pekerjaan: '',
            pendidikan: '',
            no_hp: '',
        },
        {
            hubungan: 'ibu' as const,
            nm_ortu: '',
            nik: '',
            pekerjaan: '',
            pendidikan: '',
            no_hp: '',
        },
    ],
    kontak: [{ jenis_kontak: 'WhatsApp', nomor: '' }],
    pendaftaran: {
        no_pendaftaran: '',
        tgl_daftar: new Date().toISOString().split('T')[0],
        jalur: 'Zonasi',
        asal_sekolah: '',
    },
    foto: null as File | null,
});

function submitForm() {
    form.post('/siswa', {
        forceFormData: true,
    });
}
</script>

<template>
    <AppLayout title="Pendaftaran Siswa Baru">
        <Head title="Pendaftaran Siswa Baru - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Pendaftaran Siswa Baru"
            subtitle="Formulir komprehensif data pokok pendaftaran siswa baru SMP Kristen 1 Wonosobo"
            :breadcrumbs="[
                { label: 'Data Siswa', href: '/siswa' },
                { label: 'Pendaftaran Baru', current: true },
            ]"
        />

        <form @submit.prevent="submitForm">
            <Card padding="md">
                <template #header>
                    <Tabs
                        :tabs="tabs"
                        :active-tab="activeTab"
                        @update:active-tab="activeTab = $event"
                    />
                </template>

                <div class="py-2">
                    <FormIdentitas
                        v-show="activeTab === 'identitas'"
                        :form="form"
                        :agama-list="agamaList"
                    />
                    <FormKeluarga
                        v-show="activeTab === 'keluarga'"
                        :form="form"
                    />
                    <FormOrangTua
                        v-show="activeTab === 'orang_tua'"
                        :form="form"
                    />
                    <FormKontak v-show="activeTab === 'kontak'" :form="form" />
                    <FormUploadFoto
                        v-show="activeTab === 'foto'"
                        :form="form"
                    />
                </div>

                <template #footer>
                    <div class="flex w-full items-center justify-between">
                        <Link href="/siswa">
                            <Button variant="outline" size="sm" type="button">
                                Batal
                            </Button>
                        </Link>
                        <div class="flex items-center gap-2">
                            <Button
                                variant="primary"
                                size="md"
                                type="submit"
                                :loading="form.processing"
                            >
                                Simpan & Daftarkan Siswa
                            </Button>
                        </div>
                    </div>
                </template>
            </Card>
        </form>
    </AppLayout>
</template>
