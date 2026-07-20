<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
    availableAuthProviders,
    availableDatabaseDrivers,
    availableJavascriptRuntimes,
    availablePhpVersions,
    availableServices,
    availableStarterKits,
    availableTestingFrameworks,
} from '@/build';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import CodeBlock from '@/components/CodeBlock.vue';
import InfoTooltip from '@/components/InfoTooltip.vue';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';

const { t } = useI18n();

const props = defineProps<{
    url: string;
}>();

const locale = computed(() => usePage().props.locale as string);

const isLocal = import.meta.env.DEV;

const appName = ref('new-laravel');
const appNameError = ref('');
const selectedServices = ref([]);
const selectedStarterKit = ref('react');
const customStarterKitUrl = ref('');
const customStarterKitUrlError = ref('');
const selectedJavascriptRuntime = ref('bun');
const selectedAuth = ref('laravel');
const selectedTesting = ref('pest');
const withTeams = ref(false);
const withBoost = ref(true);
const withDevcontainer = ref(false);
const withNoNode = ref(false);
const withLivewireClassComponents = ref(false);
const selectedPhpVersion = ref('8.5');
const selectedDatabase = ref('none');
const hasManuallyChangedDatabase = ref(false);

const services = ref([...availableServices]);
const starterKit = ref([...availableStarterKits]);
const javascriptRuntime = ref([...availableJavascriptRuntimes]);
const authProvider = ref([...availableAuthProviders]);
const testingFramework = ref([...availableTestingFrameworks]);
const phpVersions = ref([...availablePhpVersions]);
const databaseDrivers = ref([...availableDatabaseDrivers]);

const toggleService = (service: string) => {
    if (selectedServices.value.includes(service)) {
        selectedServices.value = selectedServices.value.filter(
            (s) => s !== service,
        );

        return;
    }

    selectedServices.value = [...selectedServices.value, service];
};

const validateAppName = () => {
    if (!appName.value) {
        appNameError.value = t('form_errors.name_required');

        return false;
    }

    if (!/^[a-zA-Z0-9_-]+$/.test(appName.value)) {
        appNameError.value = t('form_errors.name_invalid');

        return false;
    }

    appNameError.value = '';

    return true;
};

const validateCustomUrl = () => {
    if (selectedStarterKit.value !== 'custom') {
        customStarterKitUrlError.value = '';

        return true;
    }

    if (!customStarterKitUrl.value) {
        customStarterKitUrlError.value = t('form_errors.url_required');

        return false;
    }

    try {
        new URL(customStarterKitUrl.value);
        customStarterKitUrlError.value = '';

        return true;
    } catch {
        customStarterKitUrlError.value = t('form_errors.url_invalid');

        return false;
    }
};

const databaseServiceNames = ['mysql', 'mariadb', 'pgsql'];

watch(selectedServices, (newServices) => {
    if (hasManuallyChangedDatabase.value) {
        return;
    }

    const lastDbService = newServices
        .filter((s) => databaseServiceNames.includes(s))
        .at(-1);

    selectedDatabase.value = lastDbService ?? 'none';
});

const onDatabaseChange = (value: string) => {
    hasManuallyChangedDatabase.value = true;
    selectedDatabase.value = value;
};

const laravelStarterKits = ['livewire', 'vue', 'react', 'svelte'];

const showTeams = computed(
    () =>
        laravelStarterKits.includes(selectedStarterKit.value) &&
        selectedAuth.value !== 'no-authentication',
);

const generatedUrl = computed(() => {
    const baseUrl = `${props.url}/application/build`;
    const nameParam = `?name=${encodeURIComponent(appName.value)}`;
    const serviceParams = `&services=${selectedServices.value.join(',')}`;
    const frontend = `&frontend=${selectedStarterKit.value}`;
    const javascript = `&javascript=${selectedJavascriptRuntime.value}`;
    const testing = `&testing=${selectedTesting.value}`;

    let auth = '';
    let using = '';

    if (selectedStarterKit.value !== 'custom') {
        auth = `&auth=${selectedAuth.value}`;
    }

    if (selectedStarterKit.value === 'custom' && customStarterKitUrl.value) {
        using = `&using=${encodeURIComponent(customStarterKitUrl.value)}`;
    }

    const teams = withTeams.value && showTeams.value ? '&teams' : '';
    const boost = withBoost.value ? '&boost' : '';
    const devcontainer = withDevcontainer.value ? '&devcontainer' : '';
    const noNode = withNoNode.value ? '&no-node' : '';
    const livewireClassComponents =
        selectedStarterKit.value === 'livewire' &&
        withLivewireClassComponents.value
            ? '&livewire-class-components'
            : '';
    const php = `&php=${selectedPhpVersion.value}`;
    const database =
        selectedDatabase.value !== 'none'
            ? `&database=${selectedDatabase.value}`
            : '';

    return `${baseUrl}${nameParam}${serviceParams}${frontend}${javascript}${testing}${auth}${teams}${boost}${devcontainer}${noNode}${livewireClassComponents}${php}${database}${using}`;
});

const command = computed(() => `curl -s '${generatedUrl.value}' | bash`);

const faqItems = computed(() => {
    const items = [];

    for (let i = 0; i <= 7; i++) {
        items.push({
            value: `faq-${i}`,
            question: t(`faq.q${i}`),
            answer: t(`faq.a${i}`),
        });
    }

    return items;
});
</script>

<template>
    <Head>
        <title>{{ t('hero.title') }} — {{ t('header.app_name') }}</title>
        <meta name="description" :content="t('hero.description')">
        <link rel="canonical" :href="`${props.url}/${locale}/application`">
    </Head>
    <AppHeader />
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <section class="mb-8 space-y-3">
            <h1 class="text-2xl font-bold tracking-tight">
                {{ t('hero.title') }}
            </h1>
            <p class="text-sm text-muted-foreground">
                {{ t('hero.description') }}
            </p>
            <div class="flex items-center gap-4">
                <a
                    :href="`/${locale}/package`"
                    class="inline-flex items-center rounded-md bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                >
                    {{ t('hero.package_link') }}
                </a>
                <a
                    href="#how-it-works"
                    class="inline-flex items-center rounded-md border border-input bg-background px-3 py-1.5 text-xs font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                >
                    {{ t('how_it_works.learn_link') }}
                </a>
            </div>
        </section>

        <Card class="mb-6">
            <CardContent class="space-y-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-[1fr_auto]">
                    <Field :data-invalid="!!appNameError">
                        <FieldLabel for="app-name">{{
                            t('form.application_name')
                        }}</FieldLabel>
                        <Input
                            id="app-name"
                            v-model="appName"
                            :aria-invalid="!!appNameError"
                            @blur="validateAppName"
                        />
                        <FieldError v-if="appNameError">{{
                            appNameError
                        }}</FieldError>
                    </Field>
                    <Field>
                        <FieldLabel for="php-version">{{
                            t('form.php_version')
                        }}</FieldLabel>
                        <Select v-model="selectedPhpVersion">
                            <SelectTrigger id="php-version" class="w-28">
                                <SelectValue placeholder="PHP">
                                    {{ selectedPhpVersion }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="version in phpVersions"
                                    :key="version"
                                    :value="version"
                                >
                                    {{ version }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </Field>
                </div>
                <Field>
                    <FieldLabel>{{ t('form.services') }}</FieldLabel>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-for="service in services"
                            :key="service"
                            :variant="
                                selectedServices.includes(service)
                                    ? 'default'
                                    : 'outline'
                            "
                            class="cursor-pointer gap-1.5 text-sm font-medium select-none"
                            @click="toggleService(service)"
                        >
                            <span
                                class="tabular-nums"
                                :class="
                                    selectedServices.includes(service)
                                        ? 'text-primary-foreground'
                                        : 'text-muted-foreground'
                                "
                                >[{{
                                    selectedServices.includes(service)
                                        ? '+'
                                        : '-'
                                }}]</span
                            >{{ service }}
                        </Badge>
                    </div>
                </Field>

                <Field>
                    <div class="flex items-center gap-2">
                        <Label for="database-driver">{{
                            t('form.database_driver')
                        }}</Label>
                        <InfoTooltip
                            :label="t('tooltips.database_driver_label')"
                            :tooltip="t('tooltips.database_driver')"
                        />
                    </div>
                    <Select
                        :model-value="selectedDatabase"
                        @update:model-value="onDatabaseChange"
                    >
                        <SelectTrigger id="database-driver" class="w-full">
                            <SelectValue
                                :placeholder="t('form.select_database')"
                            >
                                {{ selectedDatabase }}
                            </SelectValue>
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="none">
                                {{ t('form.database_none') }}
                            </SelectItem>
                            <SelectItem
                                v-for="driver in databaseDrivers"
                                :key="driver"
                                :value="driver"
                            >
                                {{ driver }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </Field>

                <div>
                    <CodeBlock :code="command" />
                    <p
                        v-if="isLocal"
                        class="mt-2 text-xs text-muted-foreground"
                    >
                        <span>{{ t('debug.label') }}</span>
                        <a
                            :href="generatedUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="ml-1 break-all text-foreground underline underline-offset-4"
                            >{{ t('debug.open_url') }}</a
                        >
                    </p>
                </div>
            </CardContent>
        </Card>

        <Card class="mb-6">
            <CardHeader>
                <CardTitle>{{ t('form.additional_fields') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3"
                >
                    <Field>
                        <FieldLabel for="starter-kit">{{
                            t('form.starter_kit')
                        }}</FieldLabel>
                        <Select v-model="selectedStarterKit">
                            <SelectTrigger id="starter-kit" class="w-full">
                                <SelectValue
                                    :placeholder="t('form.select_starter_kit')"
                                >
                                    {{ selectedStarterKit }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="kit in starterKit"
                                    :key="kit"
                                    :value="kit"
                                >
                                    {{ kit }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </Field>

                    <Field
                        v-if="selectedStarterKit === 'custom'"
                        :data-invalid="!!customStarterKitUrlError"
                    >
                        <FieldLabel for="custom-url">{{
                            t('form.custom_starter_kit_url')
                        }}</FieldLabel>
                        <Input
                            id="custom-url"
                            v-model="customStarterKitUrl"
                            :aria-invalid="!!customStarterKitUrlError"
                            @blur="validateCustomUrl"
                        />
                        <FieldError v-if="customStarterKitUrlError">{{
                            customStarterKitUrlError
                        }}</FieldError>
                    </Field>

                    <Field>
                        <FieldLabel for="js-runtime">{{
                            t('form.javascript_runtime')
                        }}</FieldLabel>
                        <Select v-model="selectedJavascriptRuntime">
                            <SelectTrigger id="js-runtime" class="w-full">
                                <SelectValue
                                    :placeholder="t('form.select_runtime')"
                                >
                                    {{ selectedJavascriptRuntime }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="runtime in javascriptRuntime"
                                    :key="runtime"
                                    :value="runtime"
                                >
                                    {{ runtime }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </Field>

                    <Field>
                        <FieldLabel for="testing">{{
                            t('form.testing_framework')
                        }}</FieldLabel>
                        <Select v-model="selectedTesting">
                            <SelectTrigger id="testing" class="w-full">
                                <SelectValue
                                    :placeholder="t('form.select_framework')"
                                >
                                    {{ selectedTesting }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="framework in testingFramework"
                                    :key="framework"
                                    :value="framework"
                                >
                                    {{ framework }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </Field>

                    <Field v-if="selectedStarterKit !== 'custom'">
                        <FieldLabel for="auth">{{
                            t('form.auth_provider')
                        }}</FieldLabel>
                        <Select v-model="selectedAuth">
                            <SelectTrigger id="auth" class="w-full">
                                <SelectValue
                                    :placeholder="
                                        t('form.select_auth_provider')
                                    "
                                >
                                    {{ selectedAuth }}
                                </SelectValue>
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="provider in authProvider"
                                    :key="provider"
                                    :value="provider"
                                >
                                    {{ provider }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </Field>

                    <div
                        v-if="selectedStarterKit === 'livewire'"
                        class="space-y-3"
                    >
                        <div class="flex items-center gap-2">
                            <Label for="livewire-class-components">{{
                                t('form.livewire_class_components')
                            }}</Label>
                            <InfoTooltip
                                :label="
                                    t(
                                        'tooltips.livewire_class_components_label',
                                    )
                                "
                                :tooltip="
                                    t('tooltips.livewire_class_components')
                                "
                            />
                        </div>
                        <label
                            for="livewire-class-components"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            {{ t('form.use_class_components') }}
                            <Switch
                                id="livewire-class-components"
                                v-model="withLivewireClassComponents"
                            />
                        </label>
                    </div>

                    <div class="space-y-3">
                        <Label for="boost">{{ t('form.laravel_boost') }}</Label>
                        <label
                            for="boost"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            {{ t('form.install_boost') }}
                            <Switch id="boost" v-model="withBoost" />
                        </label>
                    </div>

                    <div v-if="showTeams" class="space-y-3">
                        <div class="flex items-center gap-2">
                            <Label for="teams">{{ t('form.teams') }}</Label>
                            <InfoTooltip
                                :label="t('tooltips.teams_label')"
                                :tooltip="t('tooltips.teams')"
                            />
                        </div>
                        <label
                            for="teams"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            {{ t('form.include_teams') }}
                            <Switch id="teams" v-model="withTeams" />
                        </label>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <Label for="devcontainer">{{
                                t('form.devcontainer')
                            }}</Label>
                            <InfoTooltip
                                :label="t('tooltips.devcontainer_label')"
                                :tooltip="t('tooltips.devcontainer')"
                            />
                        </div>
                        <label
                            for="devcontainer"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            {{ t('form.include_devcontainer') }}
                            <Switch
                                id="devcontainer"
                                v-model="withDevcontainer"
                            />
                        </label>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <Label for="no-node">{{
                                t('form.skip_node')
                            }}</Label>
                            <InfoTooltip
                                :label="t('tooltips.skip_node_label')"
                                :tooltip="t('tooltips.skip_node')"
                            />
                        </div>
                        <label
                            for="no-node"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            {{ t('form.skip_node_install') }}
                            <Switch id="no-node" v-model="withNoNode" />
                        </label>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Alert v-if="selectedStarterKit === 'custom'" class="mb-6">
            <AlertTitle>{{ t('custom_kit_alert.title') }}</AlertTitle>
            <AlertDescription>
                {{
                    t('custom_kit_alert.description', {
                        link: '\x00',
                    }).split('\x00')[0]
                }}
                <a
                    href="https://github.com/tnylea/laravel-new"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-medium text-foreground underline underline-offset-4"
                    >github.com/tnylea/laravel-new</a
                >{{
                    t('custom_kit_alert.description', {
                        link: '\x00',
                    }).split('\x00')[1]
                }}
            </AlertDescription>
        </Alert>

        <section id="how-it-works" class="mb-8 scroll-mt-20 space-y-4">
            <h2 class="text-base font-semibold tracking-tight">
                {{ t('how_it_works.title') }}
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="space-y-2 rounded-sm border border-border p-4">
                    <span class="text-lg font-bold text-foreground">{{
                        t('how_it_works.step1_title')
                    }}</span>
                    <p class="text-sm text-muted-foreground">
                        {{ t('how_it_works.step1_desc') }}
                    </p>
                </div>
                <div class="space-y-2 rounded-sm border border-border p-4">
                    <span class="text-lg font-bold text-foreground">{{
                        t('how_it_works.step2_title')
                    }}</span>
                    <p class="text-sm text-muted-foreground">
                        {{ t('how_it_works.step2_desc') }}
                    </p>
                </div>
                <div class="space-y-2 rounded-sm border border-border p-4">
                    <span class="text-lg font-bold text-foreground">{{
                        t('how_it_works.step3_title')
                    }}</span>
                    <p class="text-sm text-muted-foreground">
                        {{ t('how_it_works.step3_desc') }}
                    </p>
                </div>
            </div>
        </section>

        <section class="mt-12">
            <h2 class="mb-4 text-base font-semibold tracking-tight">
                {{ t('faq.title') }}
            </h2>
            <Accordion type="single" collapsible>
                <AccordionItem
                    v-for="item in faqItems"
                    :key="item.value"
                    :value="item.value"
                >
                    <AccordionTrigger>{{ item.question }}</AccordionTrigger>
                    <AccordionContent>
                        <template v-if="item.value === 'faq-2'">
                            {{ t('faq.a2') }}
                            <a
                                href="https://github.com/tnylea/laravel-new"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="font-medium text-foreground underline underline-offset-4"
                                >{{ t('faq.a2_url') }}</a
                            >
                            {{ t('faq.a2_suffix') }}
                        </template>
                        <template v-else>
                            {{ item.answer }}
                        </template>
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </section>

        <AppFooter />
    </main>
</template>
