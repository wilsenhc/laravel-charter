import VueI18nPlugin from '@intlify/unplugin-vue-i18n/vite';
import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { resolve } from 'path';
import { defineConfig } from 'vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: true,
            fonts: [
                bunny('Geist Mono', {
                    weights: [400, 500, 600, 700],
                }),
            ],
        }),
        inertia(),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        VueI18nPlugin({
            include: resolve('resources/js/locales/**'),
        }),
        wayfinder({
            formVariants: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: false,
            outDir: 'public',
            scope: '/',
            base: '/',
            devOptions: {
                enabled: false,
            },
            manifest: {
                name: 'Charter for Laravel',
                short_name: 'Charter',
                description:
                    'Pick your services and options visually, then copy a single CLI command that scaffolds your Laravel app with Sail.',
                lang: 'en',
                id: '/',
                start_url: '/',
                scope: '/',
                display: 'standalone',
                theme_color: '#171717',
                background_color: '#171717',
                icons: [
                    {
                        src: '/pwa-64x64.png',
                        sizes: '64x64',
                        type: 'image/png',
                    },
                    {
                        src: '/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any',
                    },
                    {
                        src: '/maskable-icon-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                ],
            },
            workbox: {
                globPatterns: ['build/**/*.{js,css,woff,woff2,ttf,eot}'],
                maximumFileSizeToCacheInBytes: 4000000,
                additionalManifestEntries: [
                    { url: '/favicon.svg', revision: `charter-${Date.now()}` },
                    { url: '/robots.txt', revision: `charter-${Date.now()}` },
                    {
                        url: '/social-preview.png',
                        revision: `charter-${Date.now()}`,
                    },
                    {
                        url: '/apple-touch-icon-180x180.png',
                        revision: `charter-${Date.now()}`,
                    },
                    {
                        url: '/pwa-64x64.png',
                        revision: `charter-${Date.now()}`,
                    },
                    {
                        url: '/pwa-192x192.png',
                        revision: `charter-${Date.now()}`,
                    },
                    {
                        url: '/pwa-512x512.png',
                        revision: `charter-${Date.now()}`,
                    },
                    {
                        url: '/maskable-icon-512x512.png',
                        revision: `charter-${Date.now()}`,
                    },
                ],
            },
        }),
    ],
});
