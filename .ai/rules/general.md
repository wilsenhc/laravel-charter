---
paths:
  - vite.config.ts
---

# General

## PWA: SW/manifest outDir and precache trap
vite-plugin-pwa uses outDir: 'public' so /sw.js gets root scope (no Service-Worker-Allowed header needed), but the manifest is emitted into the build dir (/build/manifest.webmanifest) via Rollup emitFile. The SW precache hardcodes 'manifest.webmanifest' relative to scope (/manifest.webmanifest) — a 404 there fails SW install (installing -> redundant). Fix: package.json build copies public/build/manifest.webmanifest to public/manifest.webmanifest after vite build. Keep devOptions.enabled false (Laravel dev has no HTML entry).

## Keep workbox navigateFallback disabled (null)
workbox.navigateFallback must stay null. The plugin defaults it to 'index.html' (a nonexistent file here, breaking SW install), and any string target is served for EVERY navigation (online too) — precaching '/' previously captured the HomepageController 302 to /en/application and served that page for all direct navigations (e.g. /en/stats showed the Application page). Inertia <Link> XHRs were unaffected (NavigationRoute only matches document navigations). All content is server-rendered, so no offline fallback is needed.

## PWA uses injectManifest with network-first navigation
vite-plugin-pwa is configured with strategies: 'injectManifest' (srcDir resources/js, filename sw.ts). The SW (resources/js/sw.ts) precaches build assets + static files via self.__WB_MANIFEST, then registers a NavigationRoute with NetworkFirst (cacheName 'pages', 3s timeout) and a catch handler serving precached /offline.html. Never add workbox.navigateFallback — the plugin defaults it to 'index.html' (nonexistent here) and any target would be served for every navigation, reintroducing the bug where direct visits (e.g. /en/stats) showed the precached '/' shell (HomepageController 302). Regenerate with vendor/bin/sail bun run build; public/sw.js is gitignored.
