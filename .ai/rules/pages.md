---
paths:
  - 'resources/js/pages/**/*.vue'
---

# Pages

## Vue page props: always use defineProps<T>
Page components MUST ALWAYS receive props via `defineProps<XxxPageProps>()` where XxxPageProps is a local (non-exported) interface named with a page-specific prefix (e.g. BuildApplicationPageProps, GlossaryShowPageProps). ALWAYS use an interface — never pass an inline type literal to `defineProps`. The interface must NOT include `[key: string]: unknown`. Shared props (locale, auth, name, locales) come through defineProps automatically because Inertia spreads all page props onto the component. Template and script access props directly (e.g. `props.locale`, `entry.translations.title`). The `declare module 'vue' { $page: Page }` augmentation in global.d.ts was removed because it shadowed the adapter's richer `Page<PageProps & SharedPageProps>` typing — never re-add it.

## One column, no sidebars
Pages MUST use a single content column. Do NOT add a second column, sidebar, side panel, or sticky secondary column (for example a live command preview beside a form). A page's generated result (such as a builder command) MUST sit inline at the top of that column, directly below the page intro, so it is visible without scrolling while the inputs that change it follow below.
