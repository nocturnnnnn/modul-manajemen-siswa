<script setup lang="ts">
import Input from '../../../ui/Input.vue';
import Select from '../../../ui/Select.vue';
import Textarea from '../../../ui/Textarea.vue';
import type { Agama, SelectOption } from '../../../../types';

interface FormData {
    nis: string;
    nisn: string;
    nik: string;
    nm_siswa: string;
    tempat_lahir: string;
    tgl_lahir: string;
    jenis_kelamin: 'Laki-laki' | 'Perempuan';
    kd_agama: number | null;
    alamat: string;
    status_siswa?: string;
    errors: Record<string, string>;
}

const props = defineProps<{
    form: FormData;
    agamaList: Agama[];
    isEdit?: boolean;
}>();

const genderOptions: SelectOption<string>[] = [
    { label: 'Laki-laki', value: 'Laki-laki' },
    { label: 'Perempuan', value: 'Perempuan' },
];

const statusOptions: SelectOption<string>[] = [
    { label: 'Aktif', value: 'aktif' },
    { label: 'Mutasi', value: 'mutasi' },
    { label: 'Pindah Sekolah', value: 'pindah' },
    { label: 'Lulus', value: 'lulus' },
    { label: 'Alumni', value: 'alumni' },
];

const agamaOptions = props.agamaList.map((a) => ({
    label: a.nm_agama,
    value: a.kd_agama,
}));
</script>

<template>
    <div class="space-y-4">
        <!-- Baris 1: NIS, NISN, NIK -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Input
                v-model="form.nis"
                label="Nomor Induk Siswa (NIS)"
                placeholder="Contoh: 202607001"
                required
                :error="form.errors.nis"
            />
            <Input
                v-model="form.nisn"
                label="NISN (10 Digit)"
                placeholder="Contoh: 0091234567"
                required
                :error="form.errors.nisn"
            />
            <Input
                v-model="form.nik"
                label="NIK Siswa (16 Digit)"
                placeholder="Contoh: 3307010101010001"
                required
                :error="form.errors.nik"
            />
        </div>

        <!-- Baris 2: Nama Lengkap & Status Siswa -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
                <Input
                    v-model="form.nm_siswa"
                    label="Nama Lengkap Siswa"
                    placeholder="Masukkan nama lengkap siswa"
                    required
                    :error="form.errors.nm_siswa"
                />
            </div>
            <div v-if="isEdit">
                <Select
                    :model-value="form.status_siswa ?? 'aktif'"
                    label="Status Siswa"
                    :options="statusOptions"
                    required
                    :error="form.errors.status_siswa"
                    @update:model-value="
                        (val) => (form.status_siswa = String(val))
                    "
                />
            </div>
        </div>

        <!-- Baris 3: Tempat Lahir & Tanggal Lahir -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <Input
                v-model="form.tempat_lahir"
                label="Tempat Lahir"
                placeholder="Contoh: Wonosobo"
                required
                :error="form.errors.tempat_lahir"
            />
            <Input
                v-model="form.tgl_lahir"
                type="date"
                label="Tanggal Lahir"
                required
                :error="form.errors.tgl_lahir"
            />
        </div>

        <!-- Baris 4: Jenis Kelamin & Agama -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <Select
                :model-value="form.jenis_kelamin"
                label="Jenis Kelamin"
                :options="genderOptions"
                required
                :error="form.errors.jenis_kelamin"
                @update:model-value="
                    (val) =>
                        (form.jenis_kelamin = val as 'Laki-laki' | 'Perempuan')
                "
            />
            <Select
                :model-value="form.kd_agama ?? ''"
                label="Agama"
                :options="agamaOptions"
                placeholder="-- Pilih Agama Siswa --"
                required
                :error="form.errors.kd_agama"
                @update:model-value="(val) => (form.kd_agama = Number(val))"
            />
        </div>

        <!-- Baris 5: Alamat Lengkap -->
        <Textarea
            v-model="form.alamat"
            label="Alamat Tempat Tinggal"
            placeholder="Jl. / RT / RW / Dusun / Desa / Kecamatan"
            :rows="3"
            required
            :error="form.errors.alamat"
        />
    </div>
</template>
