---
paths:
  - resources/js/pwa.ts
---

# Js

## Register SW client-side only (SSR-safe)
resources/js/app.ts is both the client AND SSR entry (laravel-vite-plugin resolves ssr input to the plugin input). Never import 'virtual:pwa-register' at the top of app.ts — its generated module calls register() at import time. Instead keep the import in resources/js/pwa.ts and dynamic-import it inside the `typeof window !== 'undefined'` guard with import.meta.env.PROD, so the SSR bundle never executes it.
