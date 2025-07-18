const CACHE_NAME = 'jobcard-app-v1';
const urlsToCache = [
  '/mobile-app/jobcard/index',
  '/css/app.css',
  '/js/app.js',
  '/images/icon-192.png',
  '/images/icon-512.png'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(urlsToCache);
    })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request).then(response => {
      return response || fetch(event.request);
    })
  );
});

self.addEventListener('sync', event => {
  if (event.tag === 'sync-jobcard') {
    event.waitUntil(syncJobcardData());
  }
});

async function syncJobcardData() {
  // Implement logic to sync local data with server
  const draft = localStorage.getItem('jobcard_draft');
  if (draft) {
    const data = JSON.parse(draft);
    try {
      const response = await fetch('/mobile-app/jobcard/update', {
        method: 'POST',
        body: JSON.stringify(data),
        headers: { 'Content-Type': 'application/json' }
      });
      if (response.ok) {
        localStorage.removeItem('jobcard_draft');
      }
    } catch (error) {
      console.error('Sync failed:', error);
    }
  }
  if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js');
  });
}