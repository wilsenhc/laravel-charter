---
paths:
  - 'resources/js/pages/**/*.vue'
---

# Pages

## Vue page props: always use defineProps<T>
Page components MUST ALWAYS receive props via `defineProps<XxxPageProps>()` where XxxPageProps is a local (non-exported) interface named with a page-specific prefix (e.g. BuildApplicationPageProps, GlossaryShowPageProps). ALWAYS use an interface — never pass an inline type literal to `defineProps`. The interface must NOT include `[key: string]: unknown`. Shared props (locale, auth, name, locales) come through defineProps automatically because Inertia spreads all page props onto the component. Template and script access props directly (e.g. `props.locale`, `entry.translations.title`). The `declare module 'vue' { $page: Page }` augmentation in global.d.ts was removed because it shadowed the adapter's richer `Page<PageProps & SharedPageProps>` typing — never re-add it.
