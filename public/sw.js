const CACHE_NAME = 'menzabita-v0.2.0';
const STATIC_CACHE = 'menzabita-static-v0.2.0';
const DYNAMIC_CACHE = 'menzabita-dynamic-v0.2.0';

// Static assets that should be cached with cache-first strategy
const staticAssets = [
  '/manifest.json',
  '/logo.png',
  '/favicon.ico',
  '/pwa-install.js',
  '/sw.js'
];

// Install event - cache static resources
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        console.log('Opened static cache');
        return cache.addAll(staticAssets);
      })
  );
  self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

// Helper function to determine if request is for static asset
function isStaticAsset(url) {
  const staticExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf', '.eot'];
  const staticPaths = ['/manifest.json', '/logo.png', '/favicon.ico', '/pwa-install.js', '/sw.js'];
  
  return staticExtensions.some(ext => url.pathname.endsWith(ext)) || 
         staticPaths.some(path => url.pathname === path) ||
         url.pathname.startsWith('/build/');
}

// Helper function to determine if request should bypass cache completely
function shouldBypassCache(url) {
  // Always fetch fresh for API endpoints and dynamic pages
  return url.pathname.startsWith('/api/') || 
         url.pathname.includes('dashboard') ||
         url.pathname.includes('groups') ||
         url.pathname.includes('availability') ||
         url.pathname.includes('profile') ||
         url.search.includes('_token') ||
         url.search.includes('timestamp');
}

// Fetch event - implement different caching strategies
self.addEventListener('fetch', event => {
  const requestUrl = new URL(event.request.url);
  
  // Skip caching for non-GET requests
  if (event.request.method !== 'GET') {
    return;
  }
  
  // Skip caching for requests with authentication headers
  if (event.request.headers.get('authorization') || 
      event.request.headers.get('x-csrf-token')) {
    return;
  }

  event.respondWith(
    (async () => {
      // Strategy 1: Network-first for dynamic content that should bypass cache
      if (shouldBypassCache(requestUrl)) {
        try {
          const networkResponse = await fetch(event.request);
          return networkResponse;
        } catch (error) {
          console.log('Network failed for dynamic content:', error);
          // Fallback to cache if network fails
          const cachedResponse = await caches.match(event.request);
          return cachedResponse || new Response('Offline', { status: 503 });
        }
      }
      
      // Strategy 2: Cache-first for static assets
      if (isStaticAsset(requestUrl)) {
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) {
          return cachedResponse;
        }
        
        try {
          const networkResponse = await fetch(event.request);
          if (networkResponse && networkResponse.status === 200) {
            const responseToCache = networkResponse.clone();
            const cache = await caches.open(STATIC_CACHE);
            cache.put(event.request, responseToCache);
          }
          return networkResponse;
        } catch (error) {
          console.log('Network failed for static asset:', error);
          return new Response('Asset not available', { status: 404 });
        }
      }
      
      // Strategy 3: Network-first with short-term caching for other content
      try {
        const networkResponse = await fetch(event.request);
        if (networkResponse && networkResponse.status === 200) {
          const responseToCache = networkResponse.clone();
          const cache = await caches.open(DYNAMIC_CACHE);
          
          // Add timestamp to track cache age
          const headers = new Headers(responseToCache.headers);
          headers.set('sw-cached-at', Date.now().toString());
          
          const cachedResponse = new Response(responseToCache.body, {
            status: responseToCache.status,
            statusText: responseToCache.statusText,
            headers: headers
          });
          
          cache.put(event.request, cachedResponse);
        }
        return networkResponse;
      } catch (error) {
        console.log('Network failed, trying cache:', error);
        
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) {
          // Check if cached response is too old (older than 5 minutes)
          const cachedAt = cachedResponse.headers.get('sw-cached-at');
          if (cachedAt && (Date.now() - parseInt(cachedAt)) > 5 * 60 * 1000) {
            console.log('Cached response is too old, removing from cache');
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.delete(event.request);
            return new Response('Content not available', { status: 503 });
          }
          return cachedResponse;
        }
        
        return new Response('Offline', { status: 503 });
      }
    })()
  );
});

// Background sync for offline functionality
self.addEventListener('sync', event => {
  if (event.tag === 'background-sync') {
    event.waitUntil(doBackgroundSync());
  }
});

function doBackgroundSync() {
  // Handle background sync tasks here
  console.log('Background sync triggered');
}

// Push notifications (if needed in the future)
self.addEventListener('push', event => {
  const options = {
    body: event.data ? event.data.text() : 'New notification from MenzaBita',
    icon: '/logo.png',
    badge: '/logo.png',
    vibrate: [100, 50, 100],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    }
  };

  event.waitUntil(
    self.registration.showNotification('MenzaBita', options)
  );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
  event.notification.close();
  
  event.waitUntil(
    clients.openWindow('/')
  );
});