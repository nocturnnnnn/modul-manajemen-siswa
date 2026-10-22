<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Card from '../../components/ui/Card.vue';
import Button from '../../components/ui/Button.vue';
import Badge from '../../components/ui/Badge.vue';
import Tabs, { type TabItem } from '../../components/ui/Tabs.vue';
import SiswaStatusBadge from '../../components/modules/siswa/SiswaStatusBadge.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import DokumenList from '../../components/modules/dokumen/DokumenList.vue';
import DokumenUploadModal from '../../components/modules/dokumen/DokumenUploadModal.vue';
import DokumenVerifyModal from '../../components/modules/dokumen/DokumenVerifyModal.vue';
import type { Siswa, JenisDokumen, DokumenSiswa } from '../../types';

const props = defineProps<{
    siswa: Siswa;
    jenisDokumenList?: JenisDokumen[];
}>();

const { confirmAction } = useConfirmModal();

const activeTab = ref('biodata');

const tabs: TabItem[] = [
    { id: 'biodata', label: 'Biodata & Keluarga' },
    { id: 'ortu', label: 'Orang Tua & Wali' },
    { id: 'dokumen', label: 'Berkas Dokumen' },
    { id: 'riwayat', label: 'Riwayat Akademik' },
];

const isUploadDocOpen = ref(false);
const isVerifyDocOpen = ref(false);
const selectedDoc = ref<DokumenSiswa | null>(null);

function handleDelete() {
    confirmAction({
        title: 'Hapus Data Siswa',
        message: `Apakah Anda yakin ingin menghapus siswa "${props.siswa.nm_siswa}"? Tindakan ini bersifat permanen.`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/siswa/${props.siswa.kd_siswa}`);
        },
    });
}

function handleVerifyDoc(doc: DokumenSiswa) {
    selectedDoc.value = doc;
    isVerifyDocOpen.value = true;
}

function handleDeleteDoc(doc: DokumenSiswa) {
    confirmAction({
        title: 'Hapus Dokumen',
        message: `Hapus berkas "${doc.jenis?.nm_jenis}" milik "${props.siswa.nm_siswa}"?`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/dokumen/${doc.kd_dokumen}`);
        },
    });
}
</script>

<template>
    <AppLayout :title="`Profil: ${siswa.nm_siswa}`">
        <Head :title="`${siswa.nm_siswa} - SMP Kristen 1 Wonosobo`" />

        <PageHeader
            :title="siswa.nm_siswa"
            :subtitle="`NIS: ${siswa.nis} • NISN: ${siswa.nisn}`"
            :breadcrumbs="[
                { label: 'Data Siswa', href: '/siswa' },
                { label: siswa.nm_siswa, current: true },
            ]"
        >
            <template #actions>
                <Link href="/siswa">
                    <Button variant="outline" size="sm"> Kembali </Button>
                </Link>
                <Link :href="`/siswa/${siswa.kd_siswa}/edit`">
                    <Button variant="secondary" size="sm"> Edit Data </Button>
                </Link>
                <Button variant="danger" size="sm" @click="handleDelete">
                    Hapus
                </Button>
            </template>
        </PageHeader>

        <!-- Profile Summary Header Banner -->
        <Card padding="md" class="mb-6">
            <div
                class="flex flex-col items-center gap-6 sm:flex-row sm:items-start"
            >
                <!-- Avatar -->
                <div
                    class="flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-2 border-primary-200 bg-primary-100 text-3xl font-bold text-primary-700 shadow-sm"
                >
                    <img
                        v-if="siswa.foto"
                        :src="`/storage/${siswa.foto}`"
                        :alt="siswa.nm_siswa"
                        class="h-full w-full object-cover"
                    />
                    <span v-else>
                        {{ siswa.nm_siswa.charAt(0) }}
                    </span>
                </div>

                <!-- Info Header -->
                <div class="flex-1 space-y-2 text-center sm:text-left">
                    <div
                        class="flex flex-wrap items-center justify-center gap-2.5 sm:justify-start"
                    >
                        <h2 class="text-xl font-bold text-slate-900">
                            {{ siswa.nm_siswa }}
                        </h2>
                        <SiswaStatusBadge :status="siswa.status_siswa" />
                    </div>

                    <div
                        class="flex flex-wrap items-center justify-center gap-x-6 gap-y-1 text-xs font-medium text-slate-500 sm:justify-start"
                    >
                        <p>
                            <span class="text-slate-400">NIS:</span>
                            <strong class="text-slate-800">{{
                                siswa.nis
                            }}</strong>
                        </p>
                        <p>
                            <span class="text-slate-400">NISN:</span>
                            <strong class="text-slate-800">{{
                                siswa.nisn
                            }}</strong>
                        </p>
                        <p>
                            <span class="text-slate-400">NIK:</span>
                            <strong class="text-slate-800">{{
                                siswa.nik
                            }}</strong>
                        </p>
                        <p>
                            <span class="text-slate-400">Agama:</span>
                            <strong class="text-slate-800">{{
                                siswa.agama?.nm_agama ?? '-'
                            }}</strong>
                        </p>
                    </div>

                    <p class="pt-1 text-xs leading-relaxed text-slate-600">
                        <span class="text-slate-400">Alamat:</span>
                        {{ siswa.alamat }}
                    </p>
                </div>
            </div>
        </Card>

        <!-- Detailed Tabs Navigation -->
        <Card padding="md">
            <template #header>
                <Tabs
                    :tabs="tabs"
                    :active-tab="activeTab"
                    @update:active-tab="activeTab = $event"
                />
            </template>

            <!-- Tab 1: Biodata & Keluarga -->
            <div v-show="activeTab === 'biodata'" class="space-y-6 py-2">
                <div>
                    <h4
                        class="mb-3 border-b border-border-subtle pb-2 text-sm font-semibold text-slate-800"
                    >
                        Identitas Kependudukan
                    </h4>
                    <div class="grid grid-cols-1 gap-4 text-xs sm:grid-cols-2">
                        <div class="rounded-lg bg-surface-subtle p-3">
                            <span class="mb-1 block text-slate-400"
                                >Tempat, Tanggal Lahir</span
                            >
                            <span class="font-semibold text-slate-800"
                                >{{ siswa.tempat_lahir }},
                                {{ siswa.tgl_lahir }}</span
                            >
                        </div>
                        <div class="rounded-lg bg-surface-subtle p-3">
                            <span class="mb-1 block text-slate-400"
                                >Jenis Kelamin</span
                            >
                            <span class="font-semibold text-slate-800">{{
                                siswa.jenis_kelamin
                            }}</span>
                        </div>
                    </div>
                </div>

                <div v-if="siswa.keluarga">
                    <h4
                        class="mb-3 border-b border-border-subtle pb-2 text-sm font-semibold text-slate-800"
                    >
                        Data Kartu Keluarga (KK)
                    </h4>
                    <div class="grid grid-cols-1 gap-4 text-xs sm:grid-cols-3">
                        <div class="rounded-lg bg-surface-subtle p-3">
                            <span class="mb-1 block text-slate-400"
                                >Nomor Kartu Keluarga</span
                            >
                            <span class="font-mono font-bold text-slate-800">{{
                                siswa.keluarga.no_kk
                            }}</span>
                        </div>
                        <div class="rounded-lg bg-surface-subtle p-3">
                            <span class="mb-1 block text-slate-400"
                                >Anak Ke-</span
                            >
                            <span class="font-semibold text-slate-800">{{
                                siswa.keluarga.anak_ke
                            }}</span>
                        </div>
                        <div class="rounded-lg bg-surface-subtle p-3">
                            <span class="mb-1 block text-slate-400"
                                >Jumlah Saudara</span
                            >
                            <span class="font-semibold text-slate-800"
                                >{{
                                    siswa.keluarga.jumlah_saudara
                                }}
                                bersaudara</span
                            >
                        </div>
                    </div>
                </div>

                <div v-if="siswa.pendaftaran && siswa.pendaftaran.length > 0">
                    <h4
                        class="mb-3 border-b border-border-subtle pb-2 text-sm font-semibold text-slate-800"
                    >
                        Informasi Riwayat Pendaftaran
                    </h4>
                    <div class="grid grid-cols-1 gap-4 text-xs sm:grid-cols-3">
                        <div class="rounded-lg bg-surface-subtle p-3">
                            <span class="mb-1 block text-slate-400"
                                >Nomor Pendaftaran</span
                            >
                            <span
                                class="font-mono font-bold text-primary-700"
                                >{{ siswa.pendaftaran[0].no_pendaftaran }}</span
                            >
                        </div>
                        <div class="rounded-lg bg-surface-subtle p-3">
                            <span class="mb-1 block text-slate-400"
                                >Jalur Pendaftaran</span
                            >
                            <span class="font-semibold text-slate-800">{{
                                siswa.pendaftaran[0].jalur
                            }}</span>
                        </div>
                        <div class="rounded-lg bg-surface-subtle p-3">
                            <span class="mb-1 block text-slate-400"
                                >Asal Sekolah</span
                            >
                            <span class="font-semibold text-slate-800">{{
                                siswa.pendaftaran[0].asal_sekolah
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Orang Tua & Wali -->
            <div v-show="activeTab === 'ortu'" class="space-y-4 py-2">
                <div
                    v-for="ortu in siswa.orang_tua_wali"
                    :key="ortu.kd_ortu"
                    class="rounded-xl border border-border-subtle bg-surface-subtle/50 p-4"
                >
                    <div class="mb-3 flex items-center gap-2">
                        <Badge variant="primary" size="sm">
                            {{ ortu.hubungan.toUpperCase() }}
                        </Badge>
                        <h4 class="text-sm font-bold text-slate-900">
                            {{ ortu.nm_ortu }}
                        </h4>
                    </div>

                    <div class="grid grid-cols-1 gap-3 text-xs sm:grid-cols-4">
                        <div>
                            <span class="block text-slate-400">NIK:</span>
                            <span class="font-mono text-slate-700">{{
                                ortu.nik || '-'
                            }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400">Pekerjaan:</span>
                            <span class="text-slate-700">{{
                                ortu.pekerjaan || '-'
                            }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400"
                                >Pendidikan:</span
                            >
                            <span class="text-slate-700">{{
                                ortu.pendidikan || '-'
                            }}</span>
                        </div>
                        <div>
                            <span class="block text-slate-400"
                                >No. HP / WA:</span
                            >
                            <span class="font-mono text-slate-700">{{
                                ortu.no_hp || '-'
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Berkas Dokumen -->
            <div v-show="activeTab === 'dokumen'" class="space-y-4 py-2">
                <div
                    class="flex items-center justify-between border-b border-border-subtle pb-3"
                >
                    <div>
                        <h4 class="text-sm font-semibold text-slate-800">
                            Berkas Persyaratan Digital
                        </h4>
                        <p class="text-2xs text-slate-500">
                            Unggah dan kelola berkas pendaftaran serta identitas
                            siswa
                        </p>
                    </div>
                    <Button
                        v-if="jenisDokumenList && jenisDokumenList.length > 0"
                        variant="primary"
                        size="sm"
                        @click="isUploadDocOpen = true"
                    >
                        <svg
                            class="mr-1 -ml-0.5 h-3.5 w-3.5"
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
                        Unggah Berkas
                    </Button>
                </div>

                <DokumenList
                    :dokumen-list="siswa.dokumen || []"
                    :can-verify="true"
                    @verify="handleVerifyDoc"
                    @delete="handleDeleteDoc"
                />
            </div>

            <!-- Tab 4: Riwayat Akademik & Mutasi -->
            <div v-show="activeTab === 'riwayat'" class="space-y-4 py-2">
                <div
                    v-if="
                        siswa.kenaikan_kelas && siswa.kenaikan_kelas.length > 0
                    "
                    class="space-y-2"
                >
                    <h5
                        class="text-xs font-bold tracking-wider text-slate-500 uppercase"
                    >
                        Riwayat Kenaikan Kelas
                    </h5>
                    <div
                        v-for="k in siswa.kenaikan_kelas"
                        :key="k.kd_kenaikan"
                        class="flex items-center justify-between rounded-lg bg-surface-subtle p-3 text-xs"
                    >
                        <span>
                            Tahun Ajaran:
                            <strong
                                >{{ k.tahun_ajaran?.nm_tahun }} ({{
                                    k.tahun_ajaran?.semester
                                }})</strong
                            >
                            • Dari Kelas {{ k.kelas_asal?.nm_kelas }} ke
                            {{ k.kelas_tujuan?.nm_kelas }}
                        </span>
                        <Badge
                            :variant="
                                k.status === 'naik' ? 'success' : 'danger'
                            "
                            size="sm"
                        >
                            {{ k.status.toUpperCase() }}
                        </Badge>
                    </div>
                </div>

                <div
                    v-if="
                        siswa.pindah_sekolah && siswa.pindah_sekolah.length > 0
                    "
                    class="space-y-2"
                >
                    <h5
                        class="text-xs font-bold tracking-wider text-slate-500 uppercase"
                    >
                        Catatan Pindah Sekolah
                    </h5>
                    <div
                        v-for="p in siswa.pindah_sekolah"
                        :key="p.kd_pindah"
                        class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-xs"
                    >
                        <p class="font-bold text-rose-800">
                            Sekolah Tujuan: {{ p.sekolah_tujuan }}
                        </p>
                        <p class="text-rose-600">
                            Tanggal: {{ p.tgl_pindah }} • Alasan: {{ p.alasan }}
                        </p>
                    </div>
                </div>

                <!-- Catatan Surat Mutasi -->
                <div
                    v-if="siswa.mutasi && siswa.mutasi.length > 0"
                    class="space-y-2"
                >
                    <h5
                        class="text-xs font-bold tracking-wider text-slate-500 uppercase"
                    >
                        Catatan Surat Mutasi Siswa
                    </h5>
                    <div
                        v-for="m in siswa.mutasi"
                        :key="m.kd_mutasi"
                        class="flex items-center justify-between rounded-lg border border-amber-200 bg-amber-50/60 p-3 text-xs"
                    >
                        <div>
                            <p class="font-bold text-amber-900">
                                Surat No: {{ m.no_surat }}
                            </p>
                            <p class="text-amber-700">
                                Tanggal: {{ m.tgl_mutasi }}
                            </p>
                        </div>
                        <Badge
                            :variant="
                                m.jenis_mutasi === 'Keluar'
                                    ? 'danger'
                                    : m.jenis_mutasi === 'Masuk'
                                      ? 'success'
                                      : 'warning'
                            "
                            size="sm"
                        >
                            Mutasi {{ m.jenis_mutasi }}
                        </Badge>
                    </div>
                </div>

                <!-- Catatan Kelulusan & Alumni -->
                <div v-if="siswa.kelulusan" class="space-y-2">
                    <h5
                        class="text-xs font-bold tracking-wider text-slate-500 uppercase"
                    >
                        Catatan Kelulusan & Alumni
                    </h5>
                    <div
                        class="rounded-lg border border-emerald-200 bg-emerald-50/70 p-3 text-xs"
                    >
                        <div class="flex items-center justify-between">
                            <p class="font-bold text-emerald-900">
                                Dinyatakan Lulus Tahun Ajaran:
                                {{
                                    siswa.kelulusan.tahun_ajaran?.nm_tahun ??
                                    '-'
                                }}
                                ({{ siswa.kelulusan.tgl_lulus }})
                            </p>
                            <Badge variant="success" size="sm">LULUS</Badge>
                        </div>
                        <p class="mt-1 text-emerald-800">
                            No. Ijazah / SKL:
                            <span class="font-mono font-medium">{{
                                siswa.kelulusan.no_ijazah || 'Belum dicatat'
                            }}</span>
                        </p>
                        <div
                            v-if="siswa.kelulusan.alumni"
                            class="mt-2 border-t border-emerald-200/80 pt-2 text-emerald-900"
                        >
                            <span class="font-semibold">Pelacakan Alumni:</span>
                            {{ siswa.kelulusan.alumni.kegiatan_lanjutan }} di
                            <strong>{{
                                siswa.kelulusan.alumni.instansi
                            }}</strong>
                            <span v-if="siswa.kelulusan.alumni.kontak">
                                • Kontak:
                                {{ siswa.kelulusan.alumni.kontak }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="
                        (!siswa.kenaikan_kelas ||
                            siswa.kenaikan_kelas.length === 0) &&
                        (!siswa.pindah_sekolah ||
                            siswa.pindah_sekolah.length === 0) &&
                        (!siswa.mutasi || siswa.mutasi.length === 0) &&
                        !siswa.kelulusan
                    "
                    class="py-6 text-center text-xs text-slate-400"
                >
                    Belum ada riwayat akademik, mutasi, atau kelulusan untuk
                    siswa ini.
                </div>
            </div>
        </Card>

        <!-- Document Modals -->
        <DokumenUploadModal
            v-if="jenisDokumenList"
            :is-open="isUploadDocOpen"
            :preselected-siswa-id="siswa.kd_siswa"
            :jenis-dokumen-list="jenisDokumenList"
            @close="isUploadDocOpen = false"
        />

        <DokumenVerifyModal
            :is-open="isVerifyDocOpen"
            :dokumen="selectedDoc"
            @close="isVerifyDocOpen = false"
        />
    </AppLayout>
</template>
