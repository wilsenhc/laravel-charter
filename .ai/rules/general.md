---
paths:
  - vite.config.ts
---

# General

## PWA: SW/manifest outDir and precache trap
vite-plugin-pwa uses outDir: 'public' so /sw.js gets root scope (no Service-Worker-Allowed header needed), but the manifest is emitted into the build dir (/build/manifest.webmanifest) via Rollup emitFile. The SW precache hardcodes 'manifest.webmanifest' relative to scope (/manifest.webmanifest) — a 404 there fails SW install (installing -> redundant). Fix: package.json build copies public/build/manifest.webmanifest to public/manifest.webmanifest after vite build. Keep devOptions.enabled false (Laravel dev has no HTML entry).
