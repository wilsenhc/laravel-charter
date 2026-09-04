---
paths:
  - vite.config.ts
  - resources/js/sw.ts
  - resources/js/app.ts
  - resources/js/pwa.ts
  - resources/js/composables/useInstallPrompt.ts
  - public/offline.html
  - composer.json
---

# PWA

## Vite PWA config & build pipeline
vite-plugin-pwa: strategies 'injectManifest' (srcDir resources/js, filename sw.ts), outDir 'public' (root scope, no Service-Worker-Allowed header), devOptions.enabled false, registerType autoUpdate. sw.ts precaches the __WB_MANIFEST (build assets + statics incl. /offline.html) then registers NavigationRoute(NetworkFirst, cacheName 'pages', 3s timeout, denylist /^\/build/, /^\/\.htaccess/) with a catch handler serving precached /offline.html. NEVER add workbox.navigateFallback — the plugin defaults it to 'index.html' (missing here, breaks install) and any target is served for EVERY navigation (precaching '/' once served the HomepageController 302 shell for all direct visits, e.g. /en/stats showed the Application page). package.json build copies public/build/manifest.webmanifest → public/manifest.webmanifest (a 404 on /manifest.webmanifest fails SW install). Regenerate with vendor/bin/sail bun run build; public/sw.js is gitignored.

## SW client lifecycle: registration, dev cleanup, update checks
app.ts is both the client AND SSR entry — never import 'virtual:pwa-register' at its top (the module calls register() at import time). Keep the import in pwa.ts, dynamic-imported inside the `typeof window !== 'undefined'` guard with import.meta.env.PROD. Never remove the DEV guard in app.ts that unregisters all SWs (the stale public/sw.js would break Vite HMR and Inertia auto-reload). pwa.ts must keep the manual update checks: registration.update() on router 'navigate' events and on visibilitychange, throttled client-side to once/hour — SPA <Link> XHRs never trigger the browser's built-in check, so this is the only path that picks up a new sw.js (browsers cap real checks at ~once/24h); autoUpdate + skipWaiting/clientsClaim then auto-reload open tabs.

## Capture beforeinstallprompt at module scope
Capture beforeinstallprompt at module scope (in useInstallPrompt.ts), not in a component's onMounted — the event can fire before the Vue app mounts, and it fires only once per page load. Guard module-level window usage with `typeof window !== 'undefined'` because app.ts is also the SSR entry. Share state via the useInstallPrompt composable.
