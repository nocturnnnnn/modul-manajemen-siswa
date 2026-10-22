<script setup lang="ts">
import { ref, watch } from 'vue';
import Card from '../../ui/Card.vue';
import Select from '../../ui/Select.vue';
import Button from '../../ui/Button.vue';
import Tabs, { type TabItem } from '../../ui/Tabs.vue';
import type { Agama, Kelas, TahunAjaran } from '../../../types/models';

const props = defineProps<{
    reportType: string;
    filters: Record<string, any>;
    kelasList: Kelas[];
    tahunAjaranList: TahunAjaran[];
    agamaList: Agama[];
}>();

const emit = defineEmits<{
    (e: 'change-type', type: string): void;
    (e: 'filter', filters: Record<string, any>): void;
    (e: 'reset'): void;
}>();

const reportTabs: TabItem[] = [
    { id: 'master_siswa', label: 'Master Data Siswa' },
    { id: 'rombel_kelas', label: 'Daftar per Rombel Kelas' },
    { id: 'mutasi', label: 'Mutasi & Pindah Sekolah' },
    { id: 'kelulusan_alumni', label: 'Kelulusan & Alumni' },
];

const selectedStatus = ref(props.filters.status_siswa || '');
const selectedGender = ref(props.filters.jenis_kelamin || '');
const selectedAgama = ref(props.filters.kd_agama || '');
const selectedKelas = ref(props.filters.kd_kelas || '');
const selectedTahun = ref(props.filters.kd_tahun || '');
const selectedMutasi = ref(props.filters.jenis_mutasi || '');
const selectedKegiatan = ref(props.filters.kegiatan_lanjutan || '');

watch(
    () => props.reportType,
    () => {
        selectedStatus.value = props.filters.status_siswa || '';
        selectedGender.value = props.filters.jenis_kelamin || '';
        selectedAgama.value = props.filters.kd_agama || '';
        selectedKelas.value = props.filters.kd_kelas || '';
        selectedTahun.value = props.filters.kd_tahun || '';
        selectedMutasi.value = props.filters.jenis_mutasi || '';
        selectedKegiatan.value = props.filters.kegiatan_lanjutan || '';
    },
);

function handleApplyFilter() {
    emit('filter', {
        type: props.reportType,
        status_siswa: selectedStatus.value || undefined,
        jenis_kelamin: selectedGender.value || undefined,
        kd_agama: selectedAgama.value || undefined,
        kd_kelas: selectedKelas.value || undefined,
        kd_tahun: selectedTahun.value || undefined,
        jenis_mutasi: selectedMutasi.value || undefined,
        kegiatan_lanjutan: selectedKegiatan.value || undefined,
    });
}

function handleReset() {
    selectedStatus.value = '';
    selectedGender.value = '';
    selectedAgama.value = '';
    selectedKelas.value = '';
    selectedTahun.value = '';
    selectedMutasi.value = '';
    selectedKegiatan.value = '';
    emit('reset');
}
</script>

<template>
    <Card>
        <div class="space-y-4">
            <!-- Report Type Tabs -->
            <Tabs
                :tabs="reportTabs"
                :active-tab="reportType"
                @update:active-tab="emit('change-type', $event)"
            />

            <!-- Dynamic Filters Based on Active Report Type -->
            <div
                class="grid grid-cols-1 gap-4 pt-2 sm:grid-cols-3 lg:grid-cols-4"
            >
                <!-- Master Siswa Filters -->
                <template v-if="reportType === 'master_siswa'">
                    <Select
                        v-model="selectedStatus"
                        label="Status Siswa"
                        :options="[
                            { label: 'Semua Status Siswa', value: '' },
                            { label: 'Aktif', value: 'aktif' },
                            { label: 'Mutasi', value: 'mutasi' },
                            { label: 'Pindah Sekolah', value: 'pindah' },
                            { label: 'Lulus', value: 'lulus' },
                            { label: 'Alumni', value: 'alumni' },
                        ]"
                    />
                    <Select
                        v-model="selectedGender"
                        label="Jenis Kelamin"
                        :options="[
                            { label: 'Semua Jenis Kelamin', value: '' },
                            { label: 'Laki-laki', value: 'Laki-laki' },
                            { label: 'Perempuan', value: 'Perempuan' },
                        ]"
                    />
                    <Select
                        v-model="selectedAgama"
                        label="Agama"
                        :options="[
                            { label: 'Semua Agama', value: '' },
                            ...agamaList.map((a) => ({
                                label: a.nm_agama,
                                value: a.kd_agama,
                            })),
                        ]"
                    />
                </template>

                <!-- Rombel Kelas Filters -->
                <template v-else-if="reportType === 'rombel_kelas'">
                    <Select
                        v-model="selectedKelas"
                        label="Pilih Rombel / Kelas"
                        :options="[
                            { label: 'Semua Kelas', value: '' },
                            ...kelasList.map((k) => ({
                                label: `Kelas ${k.nm_kelas} (Tingkat ${k.tingkat})`,
                                value: k.kd_kelas,
                            })),
                        ]"
                    />
                    <Select
                        v-model="selectedTahun"
                        label="Tahun Ajaran"
                        :options="[
                            { label: 'Semua Tahun Ajaran', value: '' },
                            ...tahunAjaranList.map((t) => ({
                                label: `${t.nm_tahun} (${t.semester})`,
                                value: t.kd_tahun,
                            })),
                        ]"
                    />
                </template>

                <!-- Mutasi Filters -->
                <template v-else-if="reportType === 'mutasi'">
                    <Select
                        v-model="selectedMutasi"
                        label="Jenis Mutasi"
                        :options="[
                            { label: 'Semua Mutasi & Pindah', value: '' },
                            { label: 'Mutasi Masuk', value: 'Masuk' },
                            { label: 'Mutasi Keluar', value: 'Keluar' },
                            { label: 'Mutasi Internal', value: 'Internal' },
                            { label: 'Pindah Sekolah', value: 'pindah' },
                        ]"
                    />
                </template>

                <!-- Kelulusan & Alumni Filters -->
                <template v-else-if="reportType === 'kelulusan_alumni'">
                    <Select
                        v-model="selectedTahun"
                        label="Tahun Kelulusan"
                        :options="[
                            { label: 'Semua Tahun Ajaran', value: '' },
                            ...tahunAjaranList.map((t) => ({
                                label: `${t.nm_tahun} (${t.semester})`,
                                value: t.kd_tahun,
                            })),
                        ]"
                    />
                    <Select
                        v-model="selectedKegiatan"
                        label="Studi Lanjutan"
                        :options="[
                            { label: 'Semua Kegiatan', value: '' },
                            {
                                label: 'Melanjutkan SMA',
                                value: 'Melanjutkan SMA',
                            },
                            {
                                label: 'Melanjutkan SMK',
                                value: 'Melanjutkan SMK',
                            },
                            {
                                label: 'Melanjutkan MA / Pesantren',
                                value: 'Melanjutkan MA / Pesantren',
                            },
                            {
                                label: 'Bekerja / Wirausaha',
                                value: 'Bekerja / Wirausaha',
                            },
                        ]"
                    />
                </template>

                <!-- Filter Actions -->
                <div class="flex items-end gap-2">
                    <Button
                        variant="primary"
                        class="w-full"
                        @click="handleApplyFilter"
                    >
                        Terapkan
                    </Button>
                    <Button variant="ghost" @click="handleReset">
                        Reset
                    </Button>
                </div>
            </div>
        </div>
    </Card>
</template>
