---
paths:
  - 'resources/views/**'
---

# Views

## Inline scripts require the CSP nonce
The production CSP (SecurityHeadersMiddleware) blocks inline scripts — and only in production, so it fails silently locally. Any inline <script> in Blade must carry nonce="{{ request()->attributes->get('csp-nonce') }}", set per request by SecurityHeadersMiddleware. JSON-LD/data scripts don't need it.
