<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '../../components/common/AppLayout.vue';
import PageHeader from '../../components/common/PageHeader.vue';
import Card from '../../components/ui/Card.vue';
import Button from '../../components/ui/Button.vue';
import Badge from '../../components/ui/Badge.vue';
import Select from '../../components/ui/Select.vue';
import KelasSelector from '../../components/modules/akademik/KelasSelector.vue';
import KenaikanKelasTable, {
    type EvaluasiItem,
} from '../../components/modules/akademik/KenaikanKelasTable.vue';
import KenaikanKelasModal from '../../components/modules/akademik/KenaikanKelasModal.vue';
import { useConfirmModal } from '../../composables/useConfirmModal';
import type { Kelas, KenaikanKelas, Siswa, TahunAjaran } from '../../types';

const props = defineProps<{
    tahunAjaranList: TahunAjaran[];
    selectedTahun: TahunAjaran | null;
    kelasList: Kelas[];
    selectedKelas: Kelas | null;
    siswaList: Siswa[];
    riwayatEvaluasi: KenaikanKelas[];
    targetKelasNaik: Kelas[];
    targetKelasTinggal: Kelas[];
    filters: {
        kd_tahun?: number;
        kd_kelas?: number;
    };
}>();

const { confirmAction } = useConfirmModal();

const selectedTahunId = ref<number | ''>(props.selectedTahun?.kd_tahun ?? '');
const selectedKelasId = ref<number | ''>(props.selectedKelas?.kd_kelas ?? '');

const isModalOpen = ref(false);
const isSubmitting = ref(false);

const isGrade9 = computed(() => (props.selectedKelas?.tingkat ?? 0) >= 9);

// Internal state of evaluation decision per student
const evaluasiMap = ref<Record<number, EvaluasiItem>>({});

function initEvaluasi() {
    const map: Record<number, EvaluasiItem> = {};
    const defaultNaik = props.targetKelasNaik[0]?.kd_kelas ?? '';
    const defaultTinggal = props.targetKelasTinggal[0]?.kd_kelas ?? '';

    for (const siswa of props.siswaList) {
        // Check if student already has evaluation in this academic year
        const existing = siswa.kenaikan_kelas?.[0];
        if (existing) {
            map[siswa.kd_siswa] = {
                kd_siswa: siswa.kd_siswa,
                status: existing.status,
                kd_kelas_tujuan: existing.kd_kelas_tujuan,
            };
        } else {
            map[siswa.kd_siswa] = {
                kd_siswa: siswa.kd_siswa,
                status: isGrade9.value ? 'tinggal' : 'naik',
                kd_kelas_tujuan: isGrade9.value ? defaultTinggal : defaultNaik,
            };
        }
    }
    evaluasiMap.value = map;
}

watch(
    () => props.siswaList,
    () => {
        initEvaluasi();
    },
    { immediate: true },
);

function navigateSelection() {
    router.get(
        '/akademik/kenaikan-kelas',
        {
            kd_tahun: selectedTahunId.value || undefined,
            kd_kelas: selectedKelasId.value || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function handleUpdateItem(item: EvaluasiItem) {
    evaluasiMap.value[item.kd_siswa] = item;
}

function setAllNaik() {
    if (isGrade9.value) return;
    const defaultTarget = props.targetKelasNaik[0]?.kd_kelas ?? '';
    for (const key of Object.keys(evaluasiMap.value)) {
        const id = Number(key);
        evaluasiMap.value[id] = {
            kd_siswa: id,
            status: 'naik',
            kd_kelas_tujuan: defaultTarget,
        };
    }
}

function setAllTargetClass(kdKelas: number) {
    for (const key of Object.keys(evaluasiMap.value)) {
        const id = Number(key);
        evaluasiMap.value[id].kd_kelas_tujuan = kdKelas;
    }
}

const countNaik = computed(() => {
    return Object.values(evaluasiMap.value).filter((i) => i.status === 'naik')
        .length;
});

const countTinggal = computed(() => {
    return Object.values(evaluasiMap.value).filter(
        (i) => i.status === 'tinggal',
    ).length;
});

function submitEvaluasi() {
    if (!props.selectedTahun || !props.selectedKelas) return;

    const payload = Object.values(evaluasiMap.value);
    if (payload.length === 0) return;

    isSubmitting.value = true;
    router.post(
        '/akademik/kenaikan-kelas',
        {
            kd_tahun: props.selectedTahun.kd_tahun,
            kd_kelas_asal: props.selectedKelas.kd_kelas,
            evaluasi: payload,
        },
        {
            onFinish: () => {
                isSubmitting.value = false;
                isModalOpen.value = false;
            },
        },
    );
}

function handleDeleteRiwayat(item: KenaikanKelas) {
    confirmAction({
        title: 'Batalkan Evaluasi Kenaikan',
        message: `Hapus catatan evaluasi untuk "${item.siswa?.nm_siswa}"?`,
        confirmText: 'Ya, Hapus',
        isDanger: true,
        onConfirm: () => {
            router.delete(`/akademik/kenaikan-kelas/${item.kd_kenaikan}`);
        },
    });
}
</script>

<template>
    <AppLayout title="Evaluasi Kenaikan Kelas">
        <Head title="Evaluasi Kenaikan Kelas - SMP Kristen 1 Wonosobo" />

        <PageHeader
            title="Evaluasi Kenaikan & Tinggal Kelas"
            subtitle="Pengesahan penempatan rombel siswa tahun ajaran baru SMP Kristen 1 Wonosobo"
            :breadcrumbs="[
                { label: 'Akademik' },
                { label: 'Kenaikan Kelas', current: true },
            ]"
        />

        <!-- Filter Selector Card -->
        <Card padding="md" class="mb-6">
            <KelasSelector
                v-model:selected-tahun-id="selectedTahunId"
                v-model:selected-kelas-id="selectedKelasId"
                :tahun-ajaran-list="tahunAjaranList"
                :kelas-list="kelasList"
                @update:selected-tahun-id="navigateSelection"
                @update:selected-kelas-id="navigateSelection"
            />
        </Card>

        <!-- Stats Overview Cards -->
        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <Card padding="sm" class="border-l-4 border-l-primary-600">
                <p
                    class="text-2xs font-semibold tracking-wider text-slate-500 uppercase"
                >
                    Siswa Rombel Asal
                </p>
                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ siswaList.length }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-emerald-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-emerald-600 uppercase"
                >
                    Rekomendasi Naik
                </p>
                <p class="mt-1 text-2xl font-bold text-emerald-700">
                    {{ countNaik }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-rose-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-rose-600 uppercase"
                >
                    Tinggal Kelas
                </p>
                <p class="mt-1 text-2xl font-bold text-rose-700">
                    {{ countTinggal }}
                </p>
            </Card>

            <Card padding="sm" class="border-l-4 border-l-secondary-500">
                <p
                    class="text-2xs font-semibold tracking-wider text-secondary-600 uppercase"
                >
                    Riwayat Terproses
                </p>
                <p class="mt-1 text-2xl font-bold text-secondary-800">
                    {{ riwayatEvaluasi.length }}
                </p>
            </Card>
        </div>

        <!-- Evaluation Table & Action Toolbar -->
        <Card padding="md" class="mb-6">
            <div
                class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Daftar Siswa Kelas {{ selectedKelas?.nm_kelas }}
                    </h3>
                    <p class="text-2xs text-slate-500">
                        Tentukan status evaluasi dan pilih rombel kelas tujuan
                    </p>
                </div>

                <!-- Action Toolbar -->
                <div class="flex flex-wrap items-center gap-2">
                    <Button
                        v-if="!isGrade9 && siswaList.length > 0"
                        variant="outline"
                        size="sm"
                        @click="setAllNaik"
                    >
                        Set Semua Naik
                    </Button>

                    <div
                        v-if="
                            !isGrade9 &&
                            targetKelasNaik.length > 0 &&
                            siswaList.length > 0
                        "
                        class="w-44"
                    >
                        <Select
                            model-value=""
                            :options="
                                targetKelasNaik.map((k) => ({
                                    label: `Semua ke ${k.nm_kelas}`,
                                    value: k.kd_kelas,
                                }))
                            "
                            placeholder="Target Massal..."
                            @update:model-value="
                                setAllTargetClass(Number($event))
                            "
                        />
                    </div>

                    <Button
                        v-if="siswaList.length > 0"
                        variant="primary"
                        size="sm"
                        @click="isModalOpen = true"
                    >
                        Simpan & Sahkan
                    </Button>
                </div>
            </div>

            <KenaikanKelasTable
                :siswa-list="siswaList"
                :evaluasi-items="evaluasiMap"
                :target-kelas-naik="targetKelasNaik"
                :target-kelas-tinggal="targetKelasTinggal"
                :is-grade9="isGrade9"
                @update-item="handleUpdateItem"
            />
        </Card>

        <!-- Past / Processed History Section -->
        <Card v-if="riwayatEvaluasi.length > 0" padding="md">
            <h3 class="mb-3 text-sm font-bold text-slate-900">
                Riwayat Evaluasi Terproses ({{ selectedKelas?.nm_kelas }} •
                {{ selectedTahun?.nm_tahun }})
            </h3>
            <div
                class="overflow-x-auto rounded-xl border border-border-subtle bg-white"
            >
                <table
                    class="min-w-full divide-y divide-border-subtle text-left text-xs"
                >
                    <thead
                        class="bg-surface-subtle font-semibold tracking-wider text-slate-700 uppercase"
                    >
                        <tr>
                            <th class="px-4 py-3">Siswa</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Dari Kelas</th>
                            <th class="px-4 py-3">Ke Kelas</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border-subtle bg-white">
                        <tr
                            v-for="item in riwayatEvaluasi"
                            :key="item.kd_kenaikan"
                        >
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                {{ item.siswa?.nm_siswa }}
                            </td>
                            <td class="px-4 py-3">
                                <Badge
                                    :variant="
                                        item.status === 'naik'
                                            ? 'success'
                                            : 'danger'
                                    "
                                    size="sm"
                                >
                                    {{
                                        item.status === 'naik'
                                            ? 'NAIK KELAS'
                                            : 'TINGGAL KELAS'
                                    }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                Kelas {{ item.kelas_asal?.nm_kelas }}
                            </td>
                            <td class="px-4 py-3 font-bold text-primary-700">
                                Kelas {{ item.kelas_tujuan?.nm_kelas }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-rose-600 hover:bg-rose-50"
                                    @click="handleDeleteRiwayat(item)"
                                >
                                    Batalkan
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </Card>

        <!-- Confirmation Modal -->
        <KenaikanKelasModal
            :show="isModalOpen"
            :nama-kelas-asal="selectedKelas?.nm_kelas ?? '-'"
            :nama-tahun-ajaran="`${selectedTahun?.nm_tahun ?? '-'} (${selectedTahun?.semester ?? '-'})`"
            :count-naik="countNaik"
            :count-tinggal="countTinggal"
            :is-loading="isSubmitting"
            @close="isModalOpen = false"
            @confirm="submitEvaluasi"
        />
    </AppLayout>
</template>
