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
import type { Agama, Siswa } from '../../types';

const props = defineProps<{
    siswa: Siswa;
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

const pendaftaranFirst = props.siswa.pendaftaran?.[0];

const form = useForm({
    _method: 'put',
    nis: props.siswa.nis,
    nisn: props.siswa.nisn,
    nik: props.siswa.nik,
    nm_siswa: props.siswa.nm_siswa,
    tempat_lahir: props.siswa.tempat_lahir,
    tgl_lahir: props.siswa.tgl_lahir,
    jenis_kelamin: props.siswa.jenis_kelamin,
    kd_agama: props.siswa.kd_agama,
    alamat: props.siswa.alamat,
    status_siswa: props.siswa.status_siswa,
    keluarga: {
        no_kk: props.siswa.keluarga?.no_kk ?? '',
        anak_ke: props.siswa.keluarga?.anak_ke ?? 1,
        jumlah_saudara: props.siswa.keluarga?.jumlah_saudara ?? 0,
    },
    orang_tua:
        props.siswa.orang_tua_wali && props.siswa.orang_tua_wali.length > 0
            ? props.siswa.orang_tua_wali.map((o) => ({
                  kd_ortu: o.kd_ortu,
                  hubungan: o.hubungan,
                  nm_ortu: o.nm_ortu,
                  nik: o.nik ?? '',
                  pekerjaan: o.pekerjaan ?? '',
                  pendidikan: o.pendidikan ?? '',
                  no_hp: o.no_hp ?? '',
              }))
            : [
                  {
                      hubungan: 'ayah' as const,
                      nm_ortu: '',
                      nik: '',
                      pekerjaan: '',
                      pendidikan: '',
                      no_hp: '',
                  },
              ],
    kontak:
        props.siswa.kontak && props.siswa.kontak.length > 0
            ? props.siswa.kontak.map((k) => ({
                  kd_kontak: k.kd_kontak,
                  jenis_kontak: k.jenis_kontak,
                  nomor: k.nomor,
              }))
            : [{ jenis_kontak: 'WhatsApp', nomor: '' }],
    pendaftaran: {
        no_pendaftaran: pendaftaranFirst?.no_pendaftaran ?? '',
        tgl_daftar: pendaftaranFirst?.tgl_daftar ?? '',
        jalur: pendaftaranFirst?.jalur ?? 'Zonasi',
        asal_sekolah: pendaftaranFirst?.asal_sekolah ?? '',
    },
    foto: null as File | null,
});

function submitForm() {
    form.post(`/siswa/${props.siswa.kd_siswa}`, {
        forceFormData: true,
    });
}
</script>

<template>
    <AppLayout title="Edit Profil Siswa">
        <Head :title="`Edit ${siswa.nm_siswa} - SMP Kristen 1 Wonosobo`" />

        <PageHeader
            :title="`Edit Data: ${siswa.nm_siswa}`"
            :subtitle="`Perbarui informasi pokok siswa dengan NIS ${siswa.nis}`"
            :breadcrumbs="[
                { label: 'Data Siswa', href: '/siswa' },
                { label: siswa.nm_siswa, href: `/siswa/${siswa.kd_siswa}` },
                { label: 'Edit', current: true },
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
                        :is-edit="true"
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
                        :current-foto-url="siswa.foto"
                    />
                </div>

                <template #footer>
                    <div class="flex w-full items-center justify-between">
                        <Link :href="`/siswa/${siswa.kd_siswa}`">
                            <Button variant="outline" size="sm" type="button">
                                Batal
                            </Button>
                        </Link>
                        <Button
                            variant="primary"
                            size="md"
                            type="submit"
                            :loading="form.processing"
                        >
                            Simpan Perubahan
                        </Button>
                    </div>
                </template>
            </Card>
        </form>
    </AppLayout>
</template>
