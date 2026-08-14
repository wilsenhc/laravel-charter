import { clientsClaim, skipWaiting } from 'workbox-core';
import { cleanupOutdatedCaches, precacheAndRoute } from 'workbox-precaching';
import type { PrecacheEntry } from 'workbox-precaching';
import { NavigationRoute, registerRoute } from 'workbox-routing';
import { NetworkFirst } from 'workbox-strategies';

declare const self: { __WB_MANIFEST: PrecacheEntry[] } & typeof globalThis;

skipWaiting();
clientsClaim();

precacheAndRoute(self.__WB_MANIFEST);
cleanupOutdatedCaches();

const navigationRoute = new NavigationRoute(
    new NetworkFirst({
        cacheName: 'pages',
        networkTimeoutSeconds: 3,
    }),
    { denylist: [/^\/build/, /^\/\.htaccess/] },
);

navigationRoute.setCatchHandler(async () => {
    const fallback = await caches.match('/offline.html');

    return fallback ?? Response.error();
});

registerRoute(navigationRoute);