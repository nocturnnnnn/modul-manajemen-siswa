<script setup lang="ts">
import schoolLogo from '../../assets/1000054749.png';

defineProps<{
    title: string;
    subtitle?: string;
    tahunAjaran?: string;
    tanggalCetak?: string;
}>();
</script>

<template>
    <div
        class="mb-6 border-b-2 border-double border-slate-900 pb-4 text-center"
    >
        <!-- Logo & School Identity -->
        <div class="flex items-center justify-center gap-4">
            <img
                :src="schoolLogo"
                alt="Logo SMP Kristen 1 Wonosobo"
                class="h-20 w-20 shrink-0 object-contain print:h-20 print:w-20"
            />
            <div class="text-center">
                <h3
                    class="text-xs font-semibold tracking-widest text-slate-600 uppercase print:text-black"
                >
                    YAYASAN PENDIDIKAN KRISTEN WONOSOBO
                </h3>
                <h1
                    class="text-xl font-black tracking-wide text-slate-950 uppercase print:text-black"
                >
                    SMP KRISTEN 1 WONOSOBO
                </h1>
                <p class="text-xs text-slate-600 print:text-black">
                    Jl. Pasukan Ronggolawe No. 8, Wonosobo 56311, Jawa Tengah •
                    Telp: (0286) 321350
                </p>
                <p class="text-2xs text-slate-500 print:text-black">
                    NPSN: 20306716 • Akreditasi A • Website:
                    www.smpkristen1wonosobo.sch.id
                </p>
            </div>
        </div>

        <!-- Document Report Title -->
        <div
            class="mt-4 border-t border-slate-300 pt-3 text-center print:border-slate-800"
        >
            <h2
                class="text-sm font-bold tracking-wider text-slate-900 uppercase underline decoration-1 underline-offset-4"
            >
                {{ title }}
            </h2>
            <p v-if="subtitle" class="mt-1 text-xs text-slate-600">
                {{ subtitle }}
            </p>
            <div
                class="text-2xs mt-1 flex items-center justify-center gap-4 text-slate-500"
            >
                <span v-if="tahunAjaran">Tahun Ajaran: {{ tahunAjaran }}</span>
                <span v-if="tanggalCetak">Dicetak: {{ tanggalCetak }}</span>
            </div>
        </div>
    </div>
</template>
