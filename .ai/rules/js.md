---
paths:
  - resources/js/pwa.ts
  - 'resources/js/**'
---

# Js

## Register SW client-side only (SSR-safe)
resources/js/app.ts is both the client AND SSR entry (laravel-vite-plugin resolves ssr input to the plugin input). Never import 'virtual:pwa-register' at the top of app.ts — its generated module calls register() at import time. Instead keep the import in resources/js/pwa.ts and dynamic-import it inside the `typeof window !== 'undefined'` guard with import.meta.env.PROD, so the SSR bundle never executes it.

## Capture beforeinstallprompt at module scope
Capture beforeinstallprompt at module scope (in useInstallPrompt.ts), not in a component's onMounted — the event can fire before the Vue app mounts, and it fires only once per page load. Guard module-level window usage with `typeof window !== 'undefined'` because app.ts is also the SSR entry. Share state via the useInstallPrompt composable.

## Dev must unregister stale PWA service workers
The production SW (public/sw.js, scope /) persists in the browser and controls the page even when the Vite dev server is running, serving the stale precached shell cache-first for every navigation (workbox navigateFallback). That breaks Vite HMR and Inertia version auto-reload in dev. app.ts already unregisters all SWs when import.meta.env.DEV is true — never remove that guard.
