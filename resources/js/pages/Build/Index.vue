<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { InfoIcon } from '@lucide/vue';
import { computed, ref } from 'vue';
import {
    availableAuthProviders,
    availableJavascriptRuntimes,
    availablePhpVersions,
    availableServices,
    availableStarterKits,
    availableTestingFrameworks,
} from '@/build';
import AppearanceSwitcher from '@/components/AppearanceSwitcher.vue';
import CodeBlock from '@/components/CodeBlock.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps<{
    url: string;
}>();

const appName = ref('new-laravel-project');
const selectedServices = ref([
    'pgsql',
    'valkey',
    'typesense',
    'minio',
    'mailpit',
]);
const selectedStarterKit = ref('vue');
const customStarterKitUrl = ref('');
const customStarterKitUrlError = ref('');
const selectedJavascriptRuntime = ref('bun');
const selectedAuth = ref('laravel');
const selectedTesting = ref('pest');
const withTeams = ref(false);
const withBoost = ref(true);
const withDevcontainer = ref(false);
const selectedPhpVersion = ref('8.5');

const services = ref([...availableServices]);
const starterKit = ref([...availableStarterKits]);
const javascriptRuntime = ref([...availableJavascriptRuntimes]);
const authProvider = ref([...availableAuthProviders]);
const testingFramework = ref([...availableTestingFrameworks]);
const phpVersions = ref([...availablePhpVersions]);

const toggleService = (service: string) => {
    if (selectedServices.value.includes(service)) {
        selectedServices.value = selectedServices.value.filter(
            (s) => s !== service,
        );

        return;
    }

    selectedServices.value = [...selectedServices.value, service];
};

const validateCustomUrl = () => {
    if (selectedStarterKit.value !== 'custom') {
        customStarterKitUrlError.value = '';

        return true;
    }

    if (!customStarterKitUrl.value) {
        customStarterKitUrlError.value =
            'URL is required for custom starter kit';

        return false;
    }

    try {
        new URL(customStarterKitUrl.value);
        customStarterKitUrlError.value = '';

        return true;
    } catch {
        customStarterKitUrlError.value = 'Please enter a valid URL';

        return false;
    }
};

const laravelStarterKits = ['livewire', 'vue', 'react', 'svelte'];

const showTeams = computed(
    () =>
        laravelStarterKits.includes(selectedStarterKit.value) &&
        selectedAuth.value !== 'no-authentication',
);

const command = computed(() => {
    const baseUrl = `${props.url}/${appName.value}`;
    const serviceParams = `?services=${selectedServices.value.join(',')}`;
    const frontend = `&frontend=${selectedStarterKit.value}`;
    const javascript = `&javascript=${selectedJavascriptRuntime.value}`;
    const testing = `&testing=${selectedTesting.value}`;

    let auth = '';
    let using = '';

    if (
        selectedStarterKit.value !== 'custom' &&
        selectedAuth.value !== 'laravel'
    ) {
        auth = `&auth=${selectedAuth.value}`;
    }

    if (selectedStarterKit.value === 'custom' && customStarterKitUrl.value) {
        using = `&using=${encodeURIComponent(customStarterKitUrl.value)}`;
    }

    const teams = withTeams.value && showTeams.value ? '&teams' : '';
    const boost = withBoost.value ? '&boost' : '';
    const devcontainer = withDevcontainer.value ? '&devcontainer' : '';
    const php = `&php=${selectedPhpVersion.value}`;

    return `curl -s '${baseUrl}${serviceParams}${frontend}${javascript}${testing}${auth}${teams}${boost}${devcontainer}${php}${using}' | bash`;
});
</script>

<template>
    <header
        class="flex h-14 items-center justify-between border-b border-border px-5"
    >
        <h1 class="text-base font-bold tracking-tight">Charter for Laravel</h1>
        <AppearanceSwitcher />
    </header>
    <main class="mx-auto mt-12 w-full max-w-4xl px-5 py-7">
        <Card class="mb-6">
            <CardContent class="space-y-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-[1fr_auto]">
                    <div class="space-y-3">
                        <Label for="app-name">Application name</Label>
                        <Input id="app-name" v-model="appName" />
                    </div>
                    <div class="space-y-3">
                        <Label for="php-version">PHP Version</Label>
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
                    </div>
                </div>
                <div class="space-y-3">
                    <Label>Services</Label>
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
                </div>
            </CardContent>
        </Card>

        <Card class="mb-6">
            <CardHeader>
                <CardTitle>Additional Fields</CardTitle>
            </CardHeader>
            <CardContent>
                <div
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3"
                >
                    <div class="space-y-3">
                        <Label for="starter-kit">Starter Kit</Label>
                        <Select v-model="selectedStarterKit">
                            <SelectTrigger id="starter-kit" class="w-full">
                                <SelectValue placeholder="Select a starter kit">
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
                    </div>

                    <div
                        v-if="selectedStarterKit === 'custom'"
                        class="space-y-3"
                    >
                        <Label for="custom-url">Custom Starter Kit URL</Label>
                        <Input
                            id="custom-url"
                            v-model="customStarterKitUrl"
                            :aria-invalid="!!customStarterKitUrlError"
                            @blur="validateCustomUrl"
                        />
                        <p
                            v-if="customStarterKitUrlError"
                            class="text-sm text-destructive"
                        >
                            {{ customStarterKitUrlError }}
                        </p>
                    </div>

                    <div class="space-y-3">
                        <Label for="js-runtime">JavaScript Runtime</Label>
                        <Select v-model="selectedJavascriptRuntime">
                            <SelectTrigger id="js-runtime" class="w-full">
                                <SelectValue placeholder="Select a runtime">
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
                    </div>

                    <div class="space-y-3">
                        <Label for="testing">Testing Framework</Label>
                        <Select v-model="selectedTesting">
                            <SelectTrigger id="testing" class="w-full">
                                <SelectValue placeholder="Select a framework">
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
                    </div>

                    <div
                        v-if="selectedStarterKit !== 'custom'"
                        class="space-y-3"
                    >
                        <Label for="auth">Auth Provider</Label>
                        <Select v-model="selectedAuth">
                            <SelectTrigger id="auth" class="w-full">
                                <SelectValue
                                    placeholder="Select an auth provider"
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
                    </div>

                    <div class="space-y-3">
                        <Label for="boost">Laravel Boost</Label>
                        <label
                            for="boost"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            Install Laravel Boost
                            <Switch id="boost" v-model="withBoost" />
                        </label>
                    </div>

                    <div v-if="showTeams" class="space-y-3">
                        <div class="flex items-center gap-2">
                            <Label for="teams">Teams</Label>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex size-4 items-center justify-center text-muted-foreground hover:text-foreground"
                                        >
                                            <InfoIcon class="size-3.5" />
                                            <span class="sr-only"
                                                >What is teams support?</span
                                            >
                                        </button>
                                    </TooltipTrigger>
                                    <TooltipContent class="max-w-xs">
                                        Adds team support to your application,
                                        including team membership and team-based
                                        data scoping. Available for Laravel
                                        starter kits with built-in
                                        authentication or WorkOS.
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                        <label
                            for="teams"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            Include teams support
                            <Switch id="teams" v-model="withTeams" />
                        </label>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <Label for="devcontainer">Devcontainer</Label>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex size-4 items-center justify-center text-muted-foreground hover:text-foreground"
                                        >
                                            <InfoIcon class="size-3.5" />
                                            <span class="sr-only"
                                                >What is a devcontainer?</span
                                            >
                                        </button>
                                    </TooltipTrigger>
                                    <TooltipContent class="max-w-xs">
                                        Generates a Devcontainer configuration
                                        so your application can run in a
                                        containerized development environment,
                                        such as VS Code Dev Containers or GitHub
                                        Codespaces.
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                        <label
                            for="devcontainer"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            Include devcontainer
                            <Switch
                                id="devcontainer"
                                v-model="withDevcontainer"
                            />
                        </label>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Alert v-if="selectedStarterKit === 'custom'" class="mb-6">
            <InfoIcon class="size-4" />
            <AlertTitle>Custom Starter Kit URL</AlertTitle>
            <AlertDescription>
                Looking for a custom starter kit? Browse a collection of
                community starter kits at
                <a
                    href="https://github.com/tnylea/laravel-new"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-medium text-foreground underline underline-offset-4"
                    >github.com/tnylea/laravel-new</a
                >.
            </AlertDescription>
        </Alert>

        <CodeBlock :code="command" />

        <footer
            class="mt-12 flex flex-col items-center gap-3 border-t border-border pt-8 text-center text-sm text-muted-foreground"
        >
            <a
                href="https://github.com/wilsenhc/laravel-charter"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 transition-colors hover:text-foreground"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="size-4"
                    aria-hidden="true"
                >
                    <path
                        d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.11.79-.25.79-.56 0-.27-.01-1-.02-1.96-3.2.7-3.88-1.54-3.88-1.54-.52-1.33-1.28-1.68-1.28-1.68-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.19 1.77 1.19 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.55-.29-5.24-1.28-5.24-5.69 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11.05 11.05 0 0 1 5.8 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.11 3.05.74.81 1.19 1.84 1.19 3.1 0 4.42-2.69 5.39-5.25 5.68.41.36.78 1.06.78 2.14 0 1.55-.01 2.8-.01 3.18 0 .31.21.68.8.56A11.51 11.51 0 0 0 23.5 12C23.5 5.65 18.35.5 12 .5Z"
                    />
                </svg>
                GitHub
            </a>
            <p>
                This project is NOT affiliated with, endorsed by, or sponsored
                by Laravel or Laravel Holdings Inc.
            </p>
        </footer>
    </main>
</template>
