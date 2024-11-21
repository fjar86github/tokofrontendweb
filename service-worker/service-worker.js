const CACHE_NAME = 'online-store-cache-v3';
const urlsToCache = [
    'http://localhost/toko/public/cart.php',
    'http://localhost/toko/public/checkout.php',
    'http://localhost/toko/public/index.php',
    'http://localhost/toko/public/login.php',
    'http://localhost/toko/public/logout.php',
    'http://localhost/toko/public/order_detail.php',
    'http://localhost/toko/public/order_history.php',
    'http://localhost/toko/public/products.php',
    'http://localhost/toko/public/register.php',
    'http://localhost/toko/public/offline.html', // Pastikan file offline tersedia di cache
    'http://localhost/toko/assets/css/style.css',
    'http://localhost/toko/assets/js/app.js'
];

// Install: caching static files
self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('Caching .php and assets...');
            return cache.addAll(urlsToCache);
        }).catch((error) => console.error('Caching failed:', error))
    );
    self.skipWaiting();
});

// Activate: delete old caches
self.addEventListener('activate', (event) => {
    console.log('Service Worker activated...');
    event.waitUntil(
        caches.keys().then((cacheNames) =>
            Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            )
        ).then(() => self.clients.claim())
    );
});

// Fetch: dynamic cache with fallback
self.addEventListener('fetch', (event) => {
    console.log('Fetching:', event.request.url);
    event.respondWith(
        caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
                return cachedResponse; // Kembalikan dari cache jika ada
            }

            // Jika file tidak ada di cache, fetch dari server
            return fetch(event.request)
                .then((response) => {
                    // Mengabaikan header cache-control dan memastikan konten valid
                    if (response && response.status === 200) {
                        const clonedResponse = response.clone();
                        const contentType = response.headers.get('Content-Type');
                        
                        // Cek hanya jika file tersebut adalah HTML, CSS, atau JS
                        if (contentType && (contentType.includes('text/html') || contentType.includes('application/javascript') || contentType.includes('text/css'))) {
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(event.request, clonedResponse); // Menyimpan ke cache
                            });
                        }
                    }
                    return response;
                })
                .catch(() => {
                    // Jika fetch gagal (misalnya offline), kembalikan halaman offline
                    return caches.match('http://localhost/toko/public/offline.html');
                });
        })
    );
});

// Sync: send pending orders
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-order') {
        event.waitUntil(syncPendingOrders());
    }
});

async function syncPendingOrders() {
    const pendingOrders = await getPendingOrders();
    for (const order of pendingOrders) {
        try {
            const response = await fetch('http://localhost/online_store/api/orderv2.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(order),
            });
            if (response.ok) {
                removePendingOrder(order.id); // Hapus order yang berhasil disinkronkan
            }
        } catch (error) {
            console.error('Failed to sync order:', order.id, error);
        }
    }
}

async function getPendingOrders() {
    try {
        const orders = JSON.parse(localStorage.getItem('pending-orders')) || [];
        return Array.isArray(orders) ? orders : [];
    } catch (error) {
        console.error('Error loading pending orders:', error);
        return [];
    }
}

async function removePendingOrder(orderId) {
    let pendingOrders = await getPendingOrders();
    pendingOrders = pendingOrders.filter((order) => order.id !== orderId);
    localStorage.setItem('pending-orders', JSON.stringify(pendingOrders));
}
