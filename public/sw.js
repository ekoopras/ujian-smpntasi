self.addEventListener('install', event => {
    // langsung aktif
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// 🔥 SEMUA REQUEST LANGSUNG KE SERVER
self.addEventListener('fetch', event => {
    // jangan intercept apa pun
    return;
});
