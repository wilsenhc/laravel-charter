<script setup lang="ts">
import { HeartIcon, InfoIcon } from '@lucide/vue';
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
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps<{
    url: string;
}>();

const isLocal = import.meta.env.DEV;

const appName = ref('new-laravel');
const appNameError = ref('');
const selectedServices = ref([
    'pgsql',
    'redis',
    'typesense',
    'minio',
    'mailpit',
]);
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

const validateAppName = () => {
    if (!appName.value) {
        appNameError.value = 'Application name is required';

        return false;
    }

    if (!/^[a-zA-Z0-9_-]+$/.test(appName.value)) {
        appNameError.value =
            'Application name may only contain letters, numbers, dashes, and underscores';

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

const generatedUrl = computed(() => {
    const baseUrl = `${props.url}/build`;
    const nameParam = `?name=${encodeURIComponent(appName.value)}`;
    const serviceParams = `&services=${selectedServices.value.join(',')}`;
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
    const noNode = withNoNode.value ? '&no-node' : '';
    const livewireClassComponents =
        selectedStarterKit.value === 'livewire' && withLivewireClassComponents.value
            ? '&livewire-class-components'
            : '';
    const php = `&php=${selectedPhpVersion.value}`;

    return `${baseUrl}${nameParam}${serviceParams}${frontend}${javascript}${testing}${auth}${teams}${boost}${devcontainer}${noNode}${livewireClassComponents}${php}${using}`;
});

const command = computed(() => `curl -s '${generatedUrl.value}' | bash`);
</script>

<template>
    <header
        class="flex h-14 items-center justify-between border-b border-border px-5"
    >
        <span class="text-base font-bold tracking-tight">Charter for Laravel</span>
        <div class="flex items-center gap-3">
            <a
                href="https://github.com/wilsenhc/laravel-charter"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-2 text-sm text-muted-foreground transition-colors hover:text-foreground"
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
            <AppearanceSwitcher />
        </div>
    </header>
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <section class="mb-8 space-y-3">
            <h1 class="text-2xl font-bold tracking-tight">
                Spin up a Laravel app with Sail in one command
            </h1>
            <p class="text-sm text-muted-foreground">
                Charter simplifies creating new Laravel apps: pick your services
                and options visually, then copy a single CLI command that
                scaffolds your project with Laravel Sail from the start.
            </p>
        </section>

        <section class="mb-8 space-y-4">
            <h2 class="text-base font-semibold tracking-tight">How it works</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="space-y-2 rounded-sm border border-border p-4">
                    <span class="text-lg font-bold text-foreground">1. Configure</span>
                    <p class="text-sm text-muted-foreground">Pick your Laravel version, database, cache, mail driver, and starter kit. All the options from <code class="rounded-sm bg-muted px-1.5 py-0.5 text-xs font-medium">laravel new</code> in a visual UI.</p>
                </div>
                <div class="space-y-2 rounded-sm border border-border p-4">
                    <span class="text-lg font-bold text-foreground">2. Generate</span>
                    <p class="text-sm text-muted-foreground">Charter builds a ready-to-run bash command that combines the Laravel installer with Sail setup — no more digging through docs for the right flags.</p>
                </div>
                <div class="space-y-2 rounded-sm border border-border p-4">
                    <span class="text-lg font-bold text-foreground">3. Run</span>
                    <p class="text-sm text-muted-foreground">Copy the command, paste it into your terminal, and hit enter. Minutes later you have a fresh Laravel project with Sail, services, and your chosen stack running.</p>
                </div>
            </div>
        </section>

        <Card class="mb-6">
            <CardContent class="space-y-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-[1fr_auto]">
                    <Field :data-invalid="!!appNameError">
                        <FieldLabel for="app-name">Application name</FieldLabel>
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
                        <FieldLabel for="php-version">PHP Version</FieldLabel>
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
                    <FieldLabel>Services</FieldLabel>
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

                <div>
                    <CodeBlock :code="command" />
                    <p
                        v-if="isLocal"
                        class="mt-2 text-xs text-muted-foreground"
                    >
                        <span>Debug:</span>
                        <a
                            :href="generatedUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="ml-1 break-all text-foreground underline underline-offset-4"
                            >Open generated URL in a new tab</a
                        >
                    </p>
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
                    <Field>
                        <FieldLabel for="starter-kit">Starter Kit</FieldLabel>
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
                    </Field>

                    <Field
                        v-if="selectedStarterKit === 'custom'"
                        :data-invalid="!!customStarterKitUrlError"
                    >
                        <FieldLabel for="custom-url"
                            >Custom Starter Kit URL</FieldLabel
                        >
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
                        <FieldLabel for="js-runtime"
                            >JavaScript Runtime</FieldLabel
                        >
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
                    </Field>

                    <Field>
                        <FieldLabel for="testing">Testing Framework</FieldLabel>
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
                    </Field>

                    <Field v-if="selectedStarterKit !== 'custom'">
                        <FieldLabel for="auth">Auth Provider</FieldLabel>
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
                    </Field>

                    <div v-if="selectedStarterKit === 'livewire'" class="space-y-3">
                        <div class="flex items-center gap-2">
                            <Label for="livewire-class-components"
                                >Livewire Class Components</Label
                            >
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex size-4 items-center justify-center text-muted-foreground hover:text-foreground"
                                        >
                                            <InfoIcon class="size-3.5" />
                                            <span class="sr-only"
                                                >What are Livewire class
                                                components?</span
                                            >
                                        </button>
                                    </TooltipTrigger>
                                    <TooltipContent class="max-w-xs">
                                        Generates stand-alone Livewire class
                                        components instead of single-file
                                        components.
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                        <label
                            for="livewire-class-components"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            Use class components
                            <Switch
                                id="livewire-class-components"
                                v-model="withLivewireClassComponents"
                            />
                        </label>
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

                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <Label for="no-node">Skip Node</Label>
                            <TooltipProvider>
                                <Tooltip>
                                    <TooltipTrigger as-child>
                                        <button
                                            type="button"
                                            class="inline-flex size-4 items-center justify-center text-muted-foreground hover:text-foreground"
                                        >
                                            <InfoIcon class="size-3.5" />
                                            <span class="sr-only"
                                                >What does skip node do?</span
                                            >
                                        </button>
                                    </TooltipTrigger>
                                    <TooltipContent class="max-w-xs">
                                        Skips installing Node.js and running
                                        NPM build when the application is
                                        scaffolded. Useful for projects that
                                        do not require frontend build tooling.
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                        <label
                            for="no-node"
                            class="flex h-8 w-full cursor-pointer items-center justify-between rounded-sm bg-transparent pr-2.5 text-base transition-colors hover:bg-muted/50"
                        >
                            Skip Node.js install
                            <Switch
                                id="no-node"
                                v-model="withNoNode"
                            />
                        </label>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Alert v-if="selectedStarterKit === 'custom'" class="mb-6">
            <InfoIcon class="size-4" />
            <AlertTitle>Custom Starter Kit</AlertTitle>
            <AlertDescription>
                Looking for a custom starter kit? Browse a collection of
                community starter kits at
                <a
                    href="https://github.com/tnylea/laravel-new"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-medium text-foreground underline underline-offset-4"
                    >github.com/tnylea/laravel-new</a
                >. Note that some starter kits may not be supported and
                installation may fail unexpectedly.
            </AlertDescription>
        </Alert>

        <section class="mt-12">
            <h2 class="mb-4 text-base font-semibold tracking-tight">
                Frequently Asked Questions
            </h2>
            <Accordion type="single" collapsible>
                <AccordionItem value="what-is-charter">
                    <AccordionTrigger
                        >What is Charter for Laravel?</AccordionTrigger
                    >
                    <AccordionContent>
                        Picking the right options when spinning up a new Laravel
                        app can feel like a lot. Charter gives you a friendly UI
                        where you can pick the services, starter kit, and tools
                        you want, then hands you a ready-to-run command. No more
                        memorizing flags or digging through docs.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="how-to-use">
                    <AccordionTrigger>
                        How do I use the generated command?
                    </AccordionTrigger>
                    <AccordionContent>
                        Just copy the command, paste it into your terminal, and
                        hit enter. It downloads the Laravel installer and runs
                        it with the options you chose. Wait a minute or two and
                        you'll have a fresh project ready to go.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="starter-kit">
                    <AccordionTrigger>What is a Starter Kit?</AccordionTrigger>
                    <AccordionContent>
                        Starter kits give your new app a head start with auth, a
                        frontend setup, and common scaffolding out of the box.
                        Laravel ships kits for Livewire, Vue, React, and Svelte,
                        or you can point it at your own.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="custom-starter-kit">
                    <AccordionTrigger>
                        Can I use a custom starter kit?
                    </AccordionTrigger>
                    <AccordionContent>
                        Absolutely. Pick "custom" in the Starter Kit dropdown
                        and paste in a URL. You can browse community kits at
                        <a
                            href="https://github.com/tnylea/laravel-new"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-foreground underline underline-offset-4"
                            >github.com/tnylea/laravel-new</a
                        >. Heads-up: since these are community-maintained, some
                        may not be fully supported and installation can
                        occasionally hit a snag.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="laravel-boost">
                    <AccordionTrigger>What is Laravel Boost?</AccordionTrigger>
                    <AccordionContent>
                        Boost is Laravel's official MCP toolkit that helps you
                        build faster with AI-powered helpers. Toggle it on and
                        your new app comes with it ready to go.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="teams">
                    <AccordionTrigger>
                        What does the "Teams" option do?
                    </AccordionTrigger>
                    <AccordionContent>
                        It adds team membership and team-based data scoping to
                        your app — handy if you're building something where
                        users belong to teams or workspaces. It's available for
                        Laravel starter kits with built-in authentication or
                        WorkOS.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="devcontainer">
                    <AccordionTrigger>What is a Devcontainer?</AccordionTrigger>
                    <AccordionContent>
                        It generates a Devcontainer configuration so your app
                        can run in a containerized dev environment like VS Code
                        Dev Containers or GitHub Codespaces. Great if you want a
                        consistent, reproducible setup across machines.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="affiliation">
                    <AccordionTrigger>
                        Is Charter for Laravel affiliated with Laravel?
                    </AccordionTrigger>
                    <AccordionContent>
                        Nope. Charter is an independent, community-built project
                        and isn't affiliated with, endorsed by, or sponsored by
                        Laravel or Laravel Holdings Inc.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="data-privacy">
                    <AccordionTrigger>
                        Is my data stored or sent anywhere?
                    </AccordionTrigger>
                    <AccordionContent>
                        Not at all. Everything you configure here is generated
                        in your browser — there's no server-side storage. When
                        you're ready, the command is built entirely client-side
                        and runs locally on your machine.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="what-is-sail">
                    <AccordionTrigger>
                        What is Laravel Sail?
                    </AccordionTrigger>
                    <AccordionContent>
                        Laravel Sail is a lightweight command-line interface for
                        managing Laravel's default Docker development environment.
                        It provides a pre-configured Docker Compose setup with
                        services like MySQL, PostgreSQL, Redis, Mailpit, and
                        more — so you don't need to install PHP, a web server,
                        or other software on your local machine.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="available-services">
                    <AccordionTrigger>
                        What services does Charter support?
                    </AccordionTrigger>
                    <AccordionContent>
                        Charter supports all Sail services: MySQL, PostgreSQL,
                        MariaDB, Redis, Typesense, Meilisearch, MinIO, Mailpit,
                        and Selenium. You can toggle each one on or off and the
                        generated command will include only what you need.
                    </AccordionContent>
                </AccordionItem>
                <AccordionItem value="modify-command">
                    <AccordionTrigger>
                        Can I modify the generated command?
                    </AccordionTrigger>
                    <AccordionContent>
                        Absolutely. The generated command is a standard
                        <code>laravel new</code> invocation with Sail flags. You
                        can edit it before running, or use it as a starting point
                        and tweak the options to match your exact requirements.
                    </AccordionContent>
                </AccordionItem>
            </Accordion>
        </section>

        <footer
            class="mt-12 flex flex-col items-center gap-3 border-t border-border pt-8 text-center text-sm text-muted-foreground"
        >
            <div class="flex items-center gap-4">
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
                <a
                    href="https://paypal.me/wilsenjhc"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 transition-colors hover:text-foreground"
                >
                    <HeartIcon class="size-4" aria-hidden="true" />
                    Sponsor
                </a>
            </div>
            <p>
                This project is NOT affiliated with, endorsed by, or sponsored
                by Laravel or Laravel Holdings Inc.
            </p>
        </footer>
    </main>
</template>
