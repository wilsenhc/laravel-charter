<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { useColorMode } from '@vueuse/core';
import { onMounted, ref, computed, shallowRef, watch } from 'vue';
import type { Component } from 'vue';
import { useI18n } from 'vue-i18n';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';

const { t } = useI18n();

const locale = computed(() => usePage().props.locale as string);
const origin = typeof window !== 'undefined' ? window.location.origin : '';

const props = defineProps<{
    phpVersions: Record<string, number>;
    services: Record<string, number>;
    starterKits: Record<string, number>;
    javascriptRuntimes: Record<string, number>;
    authProviders: Record<string, number>;
    testingFrameworks: Record<string, number>;
    databaseDrivers: Record<string, number>;
    booleanOptions: {
        teams: number;
        boost: number;
        devcontainer: number;
        no_node: number;
        livewire_class_components: number;
        custom_starter_kit: number;
    };
    totalApps: number;
    totalPackages: number;
    total: number;
    packagePhpVersions: Record<string, number>;
    packageFeatureOptions: {
        config: number;
        routes: number;
        views: number;
        translations: number;
        migrations: number;
        assets: number;
        commands: number;
        facade: number;
        boost_skill: number;
    };
    filters: {
        from?: string;
        to?: string;
    };
}>();

const from = ref(props.filters.from ?? '');
const to = ref(props.filters.to ?? '');
const mounted = ref(false);

watch(
    () => props.filters,
    (filters) => {
        from.value = filters.from ?? '';
        to.value = filters.to ?? '';
    },
    { immediate: true },
);

const BarChart = shallowRef<Component | null>(null);
const RadarChart = shallowRef<Component | null>(null);
const mode = useColorMode();

const isDark = computed(() => mode.value === 'dark');

const ink = computed(() => (isDark.value ? '#ffffff' : '#171717'));
const body = computed(() => (isDark.value ? '#ffffff' : '#424245'));
const mute = computed(() => (isDark.value ? '#888888' : '#646262'));
const surfaceCard = computed(() => (isDark.value ? '#222222' : '#f1eeee'));
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

const monospaceFont =
    "'Geist Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace";

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

function makeBarData(
    label: string,
    data: Record<string, number>,
    colors: string[],
): ChartDataType {
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
            label: t('stats.label_services'),
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
            backgroundColor: isDark.value ? '#222222' : '#fdfcfc',
            titleColor: ink.value,
            bodyColor: body.value,
            borderColor: surfaceCard.value,
            borderWidth: 1,
            padding: 8,
        },
    },
    scales: {
        x: {
            ticks: {
                font: { family: monospaceFont, size: 11 },
                color: mute.value,
            },
            grid: { color: surfaceCard.value },
        },
        y: {
            beginAtZero: true,
            ticks: {
                font: { family: monospaceFont, size: 11 },
                color: mute.value,
                precision: 0,
            },
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
            backgroundColor: isDark.value ? '#222222' : '#fdfcfc',
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
            ticks: {
                font: { family: monospaceFont, size: 10 },
                color: mute.value,
                backdropColor: 'transparent',
                precision: 0,
            },
            grid: { color: surfaceCard.value },
            angleLines: { color: surfaceCard.value },
            pointLabels: {
                font: { family: monospaceFont, size: 11 },
                color: body.value,
            },
        },
    },
}));

function applyFilters() {
    const params: Record<string, string> = {};

    if (from.value) {
        params.from = from.value;
    }

    if (to.value) {
        params.to = to.value;
    }

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
    router.get(
        '/stats',
        { from: dateFrom, to: dateTo },
        { preserveState: true, replace: true },
    );
}

const appOptionsData = computed<Record<string, number>>(() => ({
    [t('stats.option_teams')]: props.booleanOptions.teams,
    [t('stats.option_boost')]: props.booleanOptions.boost,
    [t('stats.option_devcontainer')]: props.booleanOptions.devcontainer,
    [t('stats.option_skip_node')]: props.booleanOptions.no_node,
    [t('stats.option_lw_class_components')]:
        props.booleanOptions.livewire_class_components,
    [t('stats.option_custom_kit')]: props.booleanOptions.custom_starter_kit,
}));

const packageFeatureData = computed<Record<string, number>>(() => {
    const raw = props.packageFeatureOptions;

    return {
        [t('stats.package_feature_config')]: raw.config,
        [t('stats.package_feature_routes')]: raw.routes,
        [t('stats.package_feature_views')]: raw.views,
        [t('stats.package_feature_translations')]: raw.translations,
        [t('stats.package_feature_migrations')]: raw.migrations,
        [t('stats.package_feature_assets')]: raw.assets,
        [t('stats.package_feature_commands')]: raw.commands,
        [t('stats.package_feature_facade')]: raw.facade,
        [t('stats.package_feature_boost_skill')]: raw.boost_skill,
    };
});

onMounted(async () => {
    const {
        Chart: ChartJS,
        CategoryScale,
        LinearScale,
        BarElement,
        PointElement,
        RadialLinearScale,
        ArcElement,
        LineElement,
        Title,
        Tooltip,
        Legend,
        Filler,
    } = await import('chart.js');
    const { Bar, Radar } = await import('vue-chartjs');

    ChartJS.register(
        CategoryScale,
        LinearScale,
        BarElement,
        PointElement,
        RadialLinearScale,
        ArcElement,
        LineElement,
        Title,
        Tooltip,
        Legend,
        Filler,
    );

    BarChart.value = Bar as Component;
    RadarChart.value = Radar as Component;
    mounted.value = true;
});
</script>

<template>
    <Head>
        <title>{{ t('stats.title') }} — {{ t('header.app_name') }}</title>
        <meta name="description" :content="t('stats.description')">
        <link rel="canonical" :href="`${origin}/${locale}/stats`">
    </Head>
    <AppHeader />
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <Link
            :href="`/${locale}/application`"
            prefetch
            class="mb-8 inline-flex text-sm text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
        >
            &larr; {{ t('nav.back_to_charter') }}
        </Link>

        <h1 class="mb-2 text-2xl font-bold tracking-tight">
            {{ t('stats.title') }}
        </h1>
        <p class="mb-8 text-sm text-muted-foreground">
            {{ t('stats.description') }}
        </p>

        <section class="mb-8 space-y-3 rounded-sm border border-border p-4">
            <h2 class="text-sm font-semibold tracking-tight">
                {{ t('stats.filter_title') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    class="rounded-sm border border-border px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:border-foreground hover:text-foreground"
                    @click="setQuickRange(7)"
                >
                    {{ t('stats.last_7_days') }}
                </button>
                <button
                    type="button"
                    class="rounded-sm border border-border px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:border-foreground hover:text-foreground"
                    @click="setQuickRange(30)"
                >
                    {{ t('stats.last_30_days') }}
                </button>
                <button
                    type="button"
                    class="rounded-sm border border-border px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:border-foreground hover:text-foreground"
                    @click="setQuickRange(90)"
                >
                    {{ t('stats.last_3_months') }}
                </button>
                <button
                    type="button"
                    class="rounded-sm border border-border px-3 py-1.5 text-xs text-muted-foreground transition-colors hover:border-foreground hover:text-foreground"
                    @click="setQuickRange('ytd')"
                >
                    {{ t('stats.year_to_date') }}
                </button>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label for="from" class="text-xs text-muted-foreground">{{
                        t('stats.from')
                    }}</label>
                    <input
                        id="from"
                        v-model="from"
                        type="date"
                        class="h-9 rounded-sm border border-border bg-surface-soft px-3 text-sm text-foreground outline-ring/50 transition-colors focus:border-foreground"
                    />
                </div>
                <div class="flex flex-col gap-1">
                    <label for="to" class="text-xs text-muted-foreground">{{
                        t('stats.to')
                    }}</label>
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
                    {{ t('stats.apply') }}
                </button>
                <button
                    v-if="filters.from || filters.to"
                    type="button"
                    class="h-9 rounded-sm border border-border px-4 text-sm text-muted-foreground transition-colors hover:text-foreground"
                    @click="clearFilters"
                >
                    {{ t('stats.clear') }}
                </button>
            </div>
        </section>

        <div class="mb-8 grid grid-cols-3 gap-4">
            <div class="rounded-sm border border-border p-4 text-center">
                <p class="text-xs text-muted-foreground">
                    {{ t('stats.total_builds') }}
                </p>
                <p class="mt-1 text-3xl font-bold tracking-tight">{{ total }}</p>
            </div>
            <div class="rounded-sm border border-border p-4 text-center">
                <p class="text-xs text-muted-foreground">
                    {{ t('stats.total_apps') }}
                </p>
                <p class="mt-1 text-3xl font-bold tracking-tight">{{ totalApps }}</p>
            </div>
            <div class="rounded-sm border border-border p-4 text-center">
                <p class="text-xs text-muted-foreground">
                    {{ t('stats.total_packages') }}
                </p>
                <p class="mt-1 text-3xl font-bold tracking-tight">{{ totalPackages }}</p>
            </div>
        </div>

        <div v-if="mounted">
            <h2 class="mb-4 text-lg font-semibold tracking-tight">
                {{ t('stats.applications_section') }}
            </h2>

            <div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <section
                    v-if="Object.keys(services).length"
                    class="rounded-sm border border-border p-4 md:col-span-3"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_services') }}
                    </h3>
                    <div class="h-72">
                        <component
                            :is="RadarChart"
                            :data="servicesData"
                            :options="radarOptions"
                        />
                    </div>
                </section>

                <section
                    v-if="Object.keys(phpVersions).length"
                    class="rounded-sm border border-border p-4"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_php_versions') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_php_versions'),
                                    phpVersions,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>

                <section
                    v-if="Object.keys(starterKits).length"
                    class="rounded-sm border border-border p-4"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_starter_kits') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_starter_kits'),
                                    starterKits,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>

                <section
                    v-if="Object.keys(javascriptRuntimes).length"
                    class="rounded-sm border border-border p-4"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_javascript_runtimes') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_javascript_runtimes'),
                                    javascriptRuntimes,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>

                <section
                    v-if="Object.keys(authProviders).length"
                    class="rounded-sm border border-border p-4"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_auth_providers') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_auth_providers'),
                                    authProviders,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>

                <section
                    v-if="Object.keys(testingFrameworks).length"
                    class="rounded-sm border border-border p-4"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_testing_frameworks') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_testing_frameworks'),
                                    testingFrameworks,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>

                <section
                    v-if="Object.keys(databaseDrivers).length"
                    class="rounded-sm border border-border p-4"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_database_drivers') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_database_drivers'),
                                    databaseDrivers,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>

                <section
                    class="rounded-sm border border-border p-4 md:col-span-3"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_options') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_options'),
                                    appOptionsData,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>
            </div>

            <h2 class="mb-4 text-lg font-semibold tracking-tight">
                {{ t('stats.packages_section') }}
            </h2>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <section
                    v-if="Object.keys(packagePhpVersions).length"
                    class="rounded-sm border border-border p-4"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_php_versions') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_php_versions'),
                                    packagePhpVersions,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>

                <section
                    class="rounded-sm border border-border p-4 md:col-span-3"
                >
                    <h3 class="mb-4 text-sm font-semibold tracking-tight">
                        {{ t('stats.chart_package_features') }}
                    </h3>
                    <div class="h-48">
                        <component
                            :is="BarChart"
                            :data="
                                makeBarData(
                                    t('stats.label_package_features'),
                                    packageFeatureData,
                                    chartPalette,
                                )
                            "
                            :options="chartOptions"
                        />
                    </div>
                </section>
            </div>
        </div>

        <div
            v-else
            class="flex items-center justify-center py-12 text-sm text-muted-foreground"
        >
            {{ t('stats.loading') }}
        </div>

        <AppFooter />
    </main>
</template>
