// sw.js - PERBAIKI INI
const CACHE_NAME = 'smk-ti-gnc-v2.0';
const urlsToCache = [
  './',
  './index.php',
  './guru.php',
  './admin.php', 
  './jurusan-rpl.php',
  './manifest.json',
  './icons/favicon-96x96.png',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

self.addEventListener('install', event => {
  console.log('🟡 Installing Service Worker...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('🟡 Caching resources...');
        return cache.addAll(urlsToCache).catch(error => {
          console.log('🔴 Cache failed for some resources:', error);
          // Continue even if some resources fail to cache
        });
      })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Return cached version or fetch from network
        return response || fetch(event.request);
      })
  );
});