const CACHE_NAME = 'SMK TI Garuda Nusantara-cache-v2';
const urlsToCache = [
  '/pkl/',
  '/pkl/index.php',
  '/pkl/ppdb.php',
  '/pkl/artikel.php',
  '/pkl/jurusan-rpl.php',
  '/pkl/pendaftaran.php',
  '/pkl/artikel-detail.php',
  '/pkl/icons/android-chrome-192x192.png',
  '/pkl/icons/android-chrome-512x512.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }
        return fetch(event.request);
      })
  );
});

// Tambahkan event listener untuk update
self.addEventListener('message', event => {
  if (event.data === 'skipWaiting') {
    self.skipWaiting();
  }
});