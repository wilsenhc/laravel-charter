import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import es from './locales/es.json';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: { en, es },
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        const serverLocale = props.initialPage.props?.locale as string | undefined;

        if (serverLocale) {
            i18n.global.locale.value = serverLocale;
        }

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n);

        if (typeof window !== 'undefined') {
            app.mount(el);
        }

        return app;
    },
});
