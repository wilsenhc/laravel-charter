<script setup lang="ts">
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    availablePackageFeatures,
    availablePhpVersions,
} from '@/build';
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
import CodeBlock from '@/components/CodeBlock.vue';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Badge } from '@/components/ui/badge';
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

const { t } = useI18n();

const props = defineProps<{
    url: string;
}>();

const isLocal = import.meta.env.DEV;

const packageName = ref('my-package');
const packageNameError = ref('');
const selectedPhpVersion = ref('8.5');
const selectedFeatures = ref<string[]>([]);
const authorName = ref('');
const authorEmail = ref('');
const composerPackageName = ref('');
const packageNameHuman = ref('');
const packageDescription = ref('');
const vendorNamespace = ref('');
const className = ref('');

const features = ref([...availablePackageFeatures]);
const phpVersions = ref([...availablePhpVersions]);

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

const generatedUrl = computed(() => {
    const baseUrl = `${props.url}/package/build`;
    const nameParam = `?name=${encodeURIComponent(packageName.value)}`;
    const php = `&php=${selectedPhpVersion.value}`;
    const features = selectedFeatures.value.length > 0
        ? `&features=${selectedFeatures.value.join(',')}`
        : '';

    const params: string[] = [];

    if (authorName.value) params.push(`author_name=${encodeURIComponent(authorName.value)}`);
    if (authorEmail.value) params.push(`author_email=${encodeURIComponent(authorEmail.value)}`);
    if (composerPackageName.value) params.push(`package_name=${encodeURIComponent(composerPackageName.value)}`);
    if (packageNameHuman.value) params.push(`package_name_human=${encodeURIComponent(packageNameHuman.value)}`);
    if (packageDescription.value) params.push(`package_description=${encodeURIComponent(packageDescription.value)}`);
    if (vendorNamespace.value) params.push(`vendor_namespace=${encodeURIComponent(vendorNamespace.value)}`);
    if (className.value) params.push(`class_name=${encodeURIComponent(className.value)}`);

    const metadata = params.length > 0 ? `&${params.join('&')}` : '';

    return `${baseUrl}${nameParam}${php}${features}${metadata}`;
});

const command = computed(() => `curl -s '${generatedUrl.value}' | bash`);
</script>

<template>
    <AppHeader />
    <main class="mx-auto w-full max-w-4xl px-5 py-7">
        <section class="mb-8 space-y-3">
            <h1 class="text-2xl font-bold tracking-tight">
                {{ t('package_hero.title') }}
            </h1>
            <p class="text-sm text-muted-foreground">
                {{ t('package_hero.description') }}
            </p>
            <a
                href="/"
                class="inline-flex text-xs text-muted-foreground underline underline-offset-4 transition-colors hover:text-foreground"
            >
                &larr; {{ t('nav.back_to_charter') }}
            </a>
        </section>

        <Card class="mb-6">
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
                            @blur="validatePackageName"
                        />
                        <FieldError v-if="packageNameError">{{
                            packageNameError
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
                    <FieldLabel>{{ t('package_form.description') }}</FieldLabel>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-for="feature in features"
                            :key="feature"
                            :variant="
                                selectedFeatures.includes(feature)
                                    ? 'default'
                                    : 'outline'
                            "
                            class="cursor-pointer gap-1.5 text-sm font-medium select-none"
                            @click="toggleFeature(feature)"
                        >
                            <span
                                class="tabular-nums"
                                :class="
                                    selectedFeatures.includes(feature)
                                        ? 'text-primary-foreground'
                                        : 'text-muted-foreground'
                                "
                                >[{{
                                    selectedFeatures.includes(feature)
                                        ? '+'
                                        : '-'
                                }}]</span
                            >{{ feature }}
                        </Badge>
                    </div>
                </Field>

                <div>
                    <CodeBlock :code="command" />
                    <p class="mt-2 text-xs text-muted-foreground">
                        {{ t('package_hero.docker_notice') }}
                    </p>
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
                <CardTitle>{{ t('package_form.metadata_title') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <div
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3"
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
                        <Input id="author-email" v-model="authorEmail" type="email" />
                    </Field>

                    <Field>
                        <FieldLabel for="composer-package-name">{{
                            t('package_form.package_name')
                        }}</FieldLabel>
                        <Input
                            id="composer-package-name"
                            v-model="composerPackageName"
                        />
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

                    <Field class="sm:col-span-2 md:col-span-3">
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
            </CardContent>
        </Card>

        <AppFooter />
    </main>
</template>
