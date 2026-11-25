<script setup lang="ts">
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import type { AvailablePackageFeature, AvailablePhpVersion } from '@/build';
import { availablePackageFeatures, availablePhpVersions } from '@/build';
import CodeBlock from '@/components/CodeBlock.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface BuildPackagePageProps {
    locale: string;
    url: string;
    presets?: {
        name?: string;
        features?: AvailablePackageFeature[];
        php?: AvailablePhpVersion;
    };
}

const { t } = useI18n();
const props = defineProps<BuildPackagePageProps>();

const isLocal = import.meta.env.DEV;

const preset = props.presets ?? {};

const packageName = ref(preset.name ?? 'my-package');
const packageNameError = ref('');
const composerPackageNameError = ref('');
const selectedPhpVersion = ref(preset.php ?? '8.5');
const selectedFeatures = ref<string[]>([...(preset.features ?? [])]);
const authorName = ref('');
const authorEmail = ref('');
const composerPackageName = ref('');
const packageNameHuman = ref('');
const packageDescription = ref('');
const vendorNamespace = ref('');
const className = ref('');

const toggleFeature = (feature: string) => {
    if (selectedFeatures.value.includes(feature)) {
        selectedFeatures.value = selectedFeatures.value.filter(
            (f) => f !== feature,
        );

        return;
    }

    selectedFeatures.value = [...selectedFeatures.value, feature];
};

const validatePackageName = () => {
    if (!packageName.value) {
        packageNameError.value = t('form_errors.name_required');

        return false;
    }

    if (!/^[a-zA-Z0-9_-]+$/.test(packageName.value)) {
        packageNameError.value = t('form_errors.name_invalid');

        return false;
    }

    packageNameError.value = '';

    return true;
};

const validateComposerPackageName = () => {
    if (!composerPackageName.value) {
        composerPackageNameError.value = '';

        return true;
    }

    if (!/^[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$/.test(composerPackageName.value)) {
        composerPackageNameError.value = t('form_errors.package_name_invalid');

        return false;
    }

    composerPackageNameError.value = '';

    return true;
};

const packageNameIsValid = (value: string): boolean =>
    value.length > 0 && /^[a-zA-Z0-9_-]+$/.test(value);

const composerPackageNameIsValid = (value: string): boolean =>
    value === '' || /^[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+$/.test(value);

const canCopy = computed(
    () =>
        packageNameIsValid(packageName.value) &&
        composerPackageNameIsValid(composerPackageName.value),
);

const generatedUrl = computed(() => {
    const baseUrl = `${props.url}/package/build`;
    const nameParam = `?name=${encodeURIComponent(packageName.value)}`;
    const php = `&php=${selectedPhpVersion.value}`;
    const features =
        selectedFeatures.value.length > 0
            ? `&features=${selectedFeatures.value.join(',')}`
            : '';

    const params: string[] = [];

    if (authorName.value) {
        params.push(`author_name=${encodeURIComponent(authorName.value)}`);
    }

    if (authorEmail.value) {
        params.push(`author_email=${encodeURIComponent(authorEmail.value)}`);
    }

    if (composerPackageName.value) {
        params.push(
            `package_name=${encodeURIComponent(composerPackageName.value)}`,
        );
    }

    if (packageNameHuman.value) {
        params.push(
            `package_name_human=${encodeURIComponent(packageNameHuman.value)}`,
        );
    }

    if (packageDescription.value) {
        params.push(
            `package_description=${encodeURIComponent(packageDescription.value)}`,
        );
    }

    if (vendorNamespace.value) {
        params.push(
            `vendor_namespace=${encodeURIComponent(vendorNamespace.value)}`,
        );
    }

    if (className.value) {
        params.push(`class_name=${encodeURIComponent(className.value)}`);
    }

    const metadata = params.length > 0 ? `&${params.join('&')}` : '';

    return `${baseUrl}${nameParam}${php}${features}${metadata}`;
});

const commands = computed(() => ({
    curl: `curl -fsSL '${generatedUrl.value}' | bash`,
    wget: `wget -qO- '${generatedUrl.value}' | bash`,
}));

const commandSummary = computed(() => [
    `php ${selectedPhpVersion.value}`,
    ...selectedFeatures.value,
]);
</script>

<template>
    <main class="mx-auto w-full max-w-5xl px-5 py-7">
        <section class="mb-8 space-y-3">
            <h1 class="text-3xl font-bold tracking-tight">
                {{ t('hero.package.title') }}
            </h1>
            <p class="max-w-prose text-sm text-muted-foreground">
                {{ t('hero.package.description') }}
            </p>
        </section>

        <div class="space-y-6">
            <div>
                <CodeBlock
                    :commands="commands"
                    :disabled="!canCopy"
                    :hint="t('code_block.package_hint')"
                    :summary="commandSummary"
                />
                <p class="mt-2 text-xs text-muted-foreground">
                    {{ t('code_block.updates_hint') }}
                </p>
                <p class="mt-1 text-xs text-muted-foreground">
                    {{ t('hero.package.docker_notice') }}
                </p>
                <p v-if="isLocal" class="mt-1 text-xs text-muted-foreground">
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

            <Card>
                <CardHeader class="border-b">
                    <CardTitle>{{ t('package_form.section') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-[1fr_auto]">
                        <Field :data-invalid="!!packageNameError">
                            <FieldLabel for="package-name">{{
                                t('package_form.title')
                            }}</FieldLabel>
                            <Input
                                id="package-name"
                                v-model="packageName"
                                :aria-invalid="!!packageNameError"
                                :aria-describedby="
                                    packageNameError
                                        ? 'package-name-error'
                                        : undefined
                                "
                                @blur="validatePackageName"
                            />
                            <FieldError
                                v-if="packageNameError"
                                id="package-name-error"
                                >{{ packageNameError }}</FieldError
                            >
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
                                        v-for="version in availablePhpVersions"
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
                        <div
                            class="flex flex-wrap items-baseline justify-between gap-2"
                        >
                            <FieldLabel id="features-label">{{
                                t('package_form.description')
                            }}</FieldLabel>
                            <span class="text-xs text-muted-foreground">{{
                                t('form.services_legend')
                            }}</span>
                        </div>
                        <div
                            role="group"
                            aria-labelledby="features-label"
                            class="flex flex-wrap gap-2"
                        >
                            <button
                                v-for="feature in availablePackageFeatures"
                                :key="feature"
                                type="button"
                                :aria-pressed="
                                    selectedFeatures.includes(feature)
                                "
                                class="inline-flex min-h-11 items-center gap-1.5 rounded-md border px-3 text-sm font-medium transition-colors outline-none select-none focus-visible:ring-3 focus-visible:ring-ring/50 sm:min-h-8"
                                :class="
                                    selectedFeatures.includes(feature)
                                        ? 'border-primary bg-primary text-primary-foreground hover:bg-primary/90'
                                        : 'border-border bg-background text-foreground hover:bg-muted'
                                "
                                @click="toggleFeature(feature)"
                            >
                                <span class="text-xs tabular-nums opacity-70"
                                    >[{{
                                        selectedFeatures.includes(feature)
                                            ? 'x'
                                            : ' '
                                    }}]</span
                                >{{ feature }}
                            </button>
                        </div>
                    </Field>
                </CardContent>
            </Card>

            <Card>
                <details class="group">
                    <summary
                        class="flex min-h-11 cursor-pointer list-none items-center gap-2 px-4 py-1 text-base font-medium select-none focus-visible:ring-3 focus-visible:ring-ring/50 focus-visible:outline-none [&::-webkit-details-marker]:hidden"
                    >
                        <span
                            class="text-xs text-muted-foreground tabular-nums"
                            aria-hidden="true"
                            >[<span class="group-open:hidden">+</span
                            ><span class="hidden group-open:inline">−</span
                            >]</span
                        >
                        {{ t('package_form.metadata_title') }}
                    </summary>
                    <div
                        class="mt-4 grid grid-cols-1 gap-5 border-t border-border px-4 pt-4 sm:grid-cols-2"
                    >
                        <Field>
                            <FieldLabel for="author-name">{{
                                t('package_form.author_name')
                            }}</FieldLabel>
                            <Input id="author-name" v-model="authorName" />
                        </Field>

                        <Field>
                            <FieldLabel for="author-email">{{
                                t('package_form.author_email')
                            }}</FieldLabel>
                            <Input
                                id="author-email"
                                v-model="authorEmail"
                                type="email"
                            />
                        </Field>

                        <Field :data-invalid="!!composerPackageNameError">
                            <FieldLabel for="composer-package-name">{{
                                t('package_form.package_name')
                            }}</FieldLabel>
                            <Input
                                id="composer-package-name"
                                v-model="composerPackageName"
                                :aria-invalid="!!composerPackageNameError"
                                :aria-describedby="
                                    composerPackageNameError
                                        ? 'composer-package-name-error'
                                        : undefined
                                "
                                @blur="validateComposerPackageName"
                            />
                            <FieldError
                                v-if="composerPackageNameError"
                                id="composer-package-name-error"
                                >{{ composerPackageNameError }}</FieldError
                            >
                        </Field>

                        <Field>
                            <FieldLabel for="package-name-human">{{
                                t('package_form.package_name_human')
                            }}</FieldLabel>
                            <Input
                                id="package-name-human"
                                v-model="packageNameHuman"
                            />
                        </Field>

                        <Field class="sm:col-span-2">
                            <FieldLabel for="package-description">{{
                                t('package_form.package_description')
                            }}</FieldLabel>
                            <Input
                                id="package-description"
                                v-model="packageDescription"
                            />
                        </Field>

                        <Field>
                            <FieldLabel for="vendor-namespace">{{
                                t('package_form.vendor_namespace')
                            }}</FieldLabel>
                            <Input
                                id="vendor-namespace"
                                v-model="vendorNamespace"
                            />
                        </Field>

                        <Field>
                            <FieldLabel for="class-name">{{
                                t('package_form.class_name')
                            }}</FieldLabel>
                            <Input id="class-name" v-model="className" />
                        </Field>
                    </div>
                </details>
            </Card>
        </div>
    </main>
</template>
