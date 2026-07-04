<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import AppFooter from '@/components/AppFooter.vue';
import { onMounted, ref, computed, shallowRef, watch, type Component } from 'vue';
import { useColorMode } from '@vueuse/core';

const props = defineProps<{
    phpVersions: Record<string, number>;
    services: Record<string, number>;
    starterKits: Record<string, number>;
    javascriptRuntimes: Record<string, number>;
    authProviders: Record<string, number>;
    testingFrameworks: Record<string, number>;
    booleanOptions: {
        teams: number;
        boost: number;
        devcontainer: number;
        no_node: number;
        livewire_class_components: number;
        custom_starter_kit: number;
    };
    total: number;
    filters: {
        from?: string;
        to?: string;
    };
}>();

const from = ref(props.filters.from ?? '');
const to = ref(props.filters.to ?? '');
const mounted = ref(false);

watch(() => props.filters, (filters) => {
    from.value = filters.from ?? '';
    to.value = filters.to ?? '';
}, { immediate: true });

const BarChart = shallowRef<Component | null>(null);
const RadarChart = shallowRef<Component | null>(null);
const mode = useColorMode();

const isDark = computed(() => mode.value === 'dark');

const ink = computed(() => (isDark.value ? '#fdfcfc' : '#201d1d'));
const body = computed(() => (isDark.value ? '#fdfcfc' : '#424245'));
const mute = computed(() => (isDark.value ? '#9a9898' : '#646262'));
const surfaceCard = computed(() => (isDark.value ? '#302c2c' : '#f1eeee'));
const accent = computed(() => (isDark.value ? '#4da6ff' : '#007aff'));
const success = computed(() => (isDark.value ? '#5dd879' : '#30d158'));
const warning = computed(() => (isDark.value ? '#ffb84d' : '#ff9f0a'));
const danger = computed(() => (isDark.value ? '#ff6b63' : '#ff3b30'));

const chartPalette = computed(() => [
    accent.value,
    success.value,
    warning.value,
    danger.value,
    ink.value,
    body.value,
    mute.value,
]);

const monospaceFont = "'Geist Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace";

type ChartDataType = {
    labels: string[];
    datasets: {
        label: string;
        data: number[];
        backgroundColor: string | string[];
        borderColor?: string | string[];
        pointBackgroundColor?: string;
        borderWidth?: number;
        fill?: boolean;
    }[];
};

function makeBarData(label: string, data: Record<string, number>, colors: string[]): ChartDataType {
    const keys = Object.keys(data);
    return {
        labels: keys,
        datasets: [
            {
                label,
                data: Object.values(data),
                backgroundColor: keys.map((_, i) => colors[i % colors.length]),
                borderWidth: 0,
            },
        ],
    };
}

const servicesData = computed<ChartDataType>(() => ({
    labels: Object.keys(props.services),
    datasets: [
        {
            label: 'Services',
            data: Object.values(props.services),
            backgroundColor: 'rgba(0, 122, 255, 0.06)',
            borderColor: accent.value,
            pointBackgroundColor: accent.value,
            borderWidth: 2,
            fill: true,
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            titleFont: { family: monospaceFont, size: 11 },
            bodyFont: { family: monospaceFont, size: 11 },
            backgroundColor: isDark.value ? '#302c2c' : '#fdfcfc',
            titleColor: ink.value,
            bodyColor: body.value,
            borderColor: surfaceCard.value,
            borderWidth: 1,
            padding: 8,
        },
    },
    scales: {
        x: {
            ticks: { font: { family: monospaceFont, size: 11 }, color: mute.value },
            grid: { color: surfaceCard.value },
        },
        y: {
            beginAtZero: true,
            ticks: { font: { family: monospaceFont, size: 11 }, color: mute.value, precision: 0 },
            grid: { color: surfaceCard.value },
        },
    },
}));

const radarOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            titleFont: { family: monospaceFont, size: 11 },
            bodyFont: { family: monospaceFont, size: 11 },
            backgroundColor: isDark.value ? '#302c2c' : '#fdfcfc',
            titleColor: ink.value,
            bodyColor: body.value,
            borderColor: surfaceCard.value,
            borderWidth: 1,
            padding: 8,
        },
    },
    scales: {
        r: {
            beginAtZero: true,
            ticks: { font: { family: monospaceFont, size: 10 }, color: mute.value, backdropColor: 'transparent', precision: 0 },
            grid: { color: surfaceCard.value },
            angleLines: { color: surfaceCard.value },
            pointLabels: { font: { family: monospaceFont, size: 11 }, color: body.value },
        },
    },
}));

function applyFilters() {
    const params: Record<string, string> = {};
    if (from.value) params.from = from.value;
    if (to.value) params.to = to.value;

    router.get('/stats', params, { preserveState: true, replace: true });
}

function clearFilters() {
    from.value = '';
    to.value = '';
    router.get('/stats', {}, { preserveState: true, replace: true });
}

function setQuickRange(days: number | 'ytd') {
    const now = new Date();
    const dateTo = now.toISOString().slice(0, 10);

    let dateFrom: string;

    if (days === 'ytd') {
        dateFrom = `${now.getFullYear()}-01-01`;
    } else if (days === 90) {
        const d = new Date(now);
        d.setMonth(d.getMonth() - 3);
        dateFrom = d.toISOString().slice(0, 10);
    } else {
        const d = new Date(now);
        d.setDate(d.getDate() - days);
        dateFrom = d.toISOString().slice(0, 10);
    }

    from.value = dateFrom;
    to.value = dateTo;
    router.get('/stats', { from: dateFrom, to: dateTo }, { preserveState: true, replace: true });
}

const optionsData = computed<Record<string, number>>(() => ({
    Teams: props.booleanOptions.teams,
    'Laravel Boost': props.booleanOptions.boost,
    Devcontainer: props.booleanOptions.devcontainer,
    'Skip Node.js': props.booleanOptions.no_node,
    'LW Class Components': props.booleanOptions.livewire_class_components,
    'Custom Kit': props.booleanOptions.custom_starter_kit,
}));

onMounted(async () => {
    const { Chart: ChartJS, CategoryScale, LinearScale, BarElement, PointElement, RadialLinearScale, ArcElement, LineElement, Title, Tooltip, Legend, Filler } = await import('chart.js');
    const { Bar, Radar } = await import('vue-chartjs');

    ChartJS.register(CategoryScale, LinearScale, BarElement, PointElement, RadialLinearScale, ArcElement, LineElement, Title, Tooltip, Legend, Filler);

    BarChart.value = Bar as Component;
    RadarChart.value = Radar as Component;
    mounted.value = true;
});
</script>

<template>
    <main class="mx-auto max-w-5xl px-4 py-12">
        <Link
            href="/"
            class="mb-8 inline-flex text-sm text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
        >
            &larr; Back to Charter
        </Link>

        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            Usage Statistics
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            Anonymous aggregate data from generated build commands.
            No personal or identifying information is collected.
        </p>

        <section class="mb-8 space-y-3 rounded-sm border border-border p-4">
            <h2 class="text-sm font-semibold tracking-tight">Filter by date</h2>
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    class="rounded-sm border border-border px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:border-foreground hover:text-foreground"
                    @click="setQuickRange(7)"
                >Last 7 days</button>
                <button
                    type="button"
                    class="rounded-sm border border-border px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:border-foreground hover:text-foreground"
                    @click="setQuickRange(30)"
                >Last 30 days</button>
                <button
                    type="button"
                    class="rounded-sm border border-border px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:border-foreground hover:text-foreground"
                    @click="setQuickRange(90)"
                >Last 3 months</button>
                <button
                    type="button"
                    class="rounded-sm border border-border px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:border-foreground hover:text-foreground"
                    @click="setQuickRange('ytd')"
                >Year to date</button>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label for="from" class="text-xs text-muted-foreground">From</label>
                    <input
                        id="from"
                        v-model="from"
                        type="date"
                        class="h-9 rounded-sm border border-border bg-surface-soft px-3 text-sm text-foreground outline-ring/50 transition-colors focus:border-foreground"
                    />
                </div>
                <div class="flex flex-col gap-1">
                    <label for="to" class="text-xs text-muted-foreground">To</label>
                    <input
                        id="to"
                        v-model="to"
                        type="date"
                        class="h-9 rounded-sm border border-border bg-surface-soft px-3 text-sm text-foreground outline-ring/50 transition-colors focus:border-foreground"
                    />
                </div>
                <button
                    type="button"
                    class="h-9 rounded-sm bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:opacity-90"
                    @click="applyFilters"
                >
                    Apply
                </button>
                <button
                    v-if="filters.from || filters.to"
                    type="button"
                    class="h-9 rounded-sm border border-border px-4 text-sm text-muted-foreground transition-colors hover:text-foreground"
                    @click="clearFilters"
                >
                    Clear
                </button>
            </div>
        </section>

        <div class="mb-8 rounded-sm border border-border p-4 text-center">
            <p class="text-xs text-muted-foreground">Total Builds</p>
            <p class="mt-1 text-3xl font-bold tracking-tight">{{ total }}</p>
        </div>

        <div v-if="mounted" class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <section v-if="Object.keys(services).length" class="rounded-sm border border-border p-4 md:col-span-3">
                <h2 class="mb-4 text-sm font-semibold tracking-tight">[Services]</h2>
                <div class="h-72">
                    <component
                        :is="RadarChart"
                        :data="servicesData"
                        :options="radarOptions"
                    />
                </div>
            </section>

            <section v-if="Object.keys(phpVersions).length" class="rounded-sm border border-border p-4">
                <h2 class="mb-4 text-sm font-semibold tracking-tight">[PHP Versions]</h2>
                <div class="h-48">
                    <component
                        :is="BarChart"
                        :data="makeBarData('PHP Versions', phpVersions, chartPalette)"
                        :options="chartOptions"
                    />
                </div>
            </section>

            <section v-if="Object.keys(starterKits).length" class="rounded-sm border border-border p-4">
                <h2 class="mb-4 text-sm font-semibold tracking-tight">[Starter Kits]</h2>
                <div class="h-48">
                    <component
                        :is="BarChart"
                        :data="makeBarData('Starter Kits', starterKits, chartPalette)"
                        :options="chartOptions"
                    />
                </div>
            </section>

            <section v-if="Object.keys(javascriptRuntimes).length" class="rounded-sm border border-border p-4">
                <h2 class="mb-4 text-sm font-semibold tracking-tight">[JavaScript Runtimes]</h2>
                <div class="h-48">
                    <component
                        :is="BarChart"
                        :data="makeBarData('JavaScript Runtimes', javascriptRuntimes, chartPalette)"
                        :options="chartOptions"
                    />
                </div>
            </section>

            <section v-if="Object.keys(authProviders).length" class="rounded-sm border border-border p-4">
                <h2 class="mb-4 text-sm font-semibold tracking-tight">[Auth Providers]</h2>
                <div class="h-48">
                    <component
                        :is="BarChart"
                        :data="makeBarData('Auth Providers', authProviders, chartPalette)"
                        :options="chartOptions"
                    />
                </div>
            </section>

            <section v-if="Object.keys(testingFrameworks).length" class="rounded-sm border border-border p-4">
                <h2 class="mb-4 text-sm font-semibold tracking-tight">[Testing Frameworks]</h2>
                <div class="h-48">
                    <component
                        :is="BarChart"
                        :data="makeBarData('Testing Frameworks', testingFrameworks, chartPalette)"
                        :options="chartOptions"
                    />
                </div>
            </section>

            <section class="rounded-sm border border-border p-4 md:col-span-3">
                <h2 class="mb-4 text-sm font-semibold tracking-tight">[Options]</h2>
                <div class="h-48">
                    <component
                        :is="BarChart"
                        :data="makeBarData('Options', optionsData, chartPalette)"
                        :options="chartOptions"
                    />
                </div>
            </section>
        </div>

        <div v-else class="flex items-center justify-center py-12 text-sm text-muted-foreground">
            Loading charts...
        </div>

        <AppFooter />
    </main>
</template>
