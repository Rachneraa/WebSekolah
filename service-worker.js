const CACHE_NAME = 'SMK TI Garuda Nusantara-cache-v2';
const urlsToCache = [
  '/',                 // root (ubah sesuai base path jika project berada di subfolder)
  '/index.php',
  '/ppdb.php',
  '/article.php',
  '/jurusan-rpl.php',
  '/pendaftaran.php',
  '/artikel-detail.php',
  '/contact.php',
  '/icons/android-chrome-192x192.png',
  '/icons/android-chrome-512x512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(urlsToCache))
      .catch((err) => {
        console.error('Cache addAll failed:', err);
      })
  );
});

self.addEventListener('activate', (event) => {
	event.waitUntil(
		caches.keys().then((cacheNames) => {
			return Promise.all(
				cacheNames.map((cacheName) => {
					if (cacheName !== CACHE_NAME) {
						return caches.delete(cacheName);
					}
				})
			);
		})
	);
});

self.addEventListener('fetch', (event) => {
	event.respondWith(
		caches.match(event.request).then((response) => {
			if (response) {
				return response;
			}
			return fetch(event.request);
		})
	);
});

// Tambahkan event listener untuk update
self.addEventListener('message', (event) => {
	if (event.data === 'skipWaiting') {
		self.skipWaiting();
	}
});
