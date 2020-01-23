// Service Worker

let cacheWhitelist = ['main-cache-v1'];

let currentCacheList = [
    '/',
    '/login',
    '/asset/img/favicons/apple-touch-icon.png',
    '/asset/img/favicons/favicon-32x32.png',
    '/asset/img/favicons/favicon-16x16.png',
    '/asset/img/favicons/site.webmanifest',
    '/asset/img/favicons/safari-pinned-tab.svg',
    '/asset/img/favicons/favicon.ico',
    '/asset/img/favicons/browserconfig.xml',
    '/asset/css/fontawesome-5.8.2.min.css',
    '/asset/css/bootstrap.min.css',
    '/asset/css/mdb.min.css',
    '/asset/css/jquery-ui.min.css',
    '/asset/css/style.css',
    '/asset/js/jquery-3.4.1.min.js',
    '/asset/js/jquery-ui.min.js',
    '/asset/js/jquery.ui.touch-punch.min.js',
    '/asset/js/popper.min.js',
    '/asset/js/bootstrap.min.js',
    '/asset/js/mdb.min.js',
    '/asset/js/script.js'
];

let commonCache = [
    '/',
    '/login',
    '/user',
    '/db',
    '/db/objects',
    '/db/clients',
    '/help'
];

self.addEventListener('install', e => {
    e.waitUntil(
        // Open new cache after service worker installing
        caches
            .open('main-cache-v1')
            .then( cache => {
                // Add all URLs, that we want add to cache
                return cache.addAll( currentCacheList );
            })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // ресурс есть в кеше
                if (response) {
                    return response;
                }

                /* Важно: клонируем запрос. Запрос - это поток, может быть обработан только раз. Если мы хотим использовать объект request несколько раз, его нужно клонировать */
                var fetchRequest = event.request.clone();

                return fetch(fetchRequest).then(
                    function(response) {
                        // проверяем, что получен корректный ответ
                        if(!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        /* ВАЖНО: Клонируем ответ. Объект response также является потоком. */
                        var responseToCache = response.clone();

                        caches.open('main-cache-v1')
                            .then(function(cache) {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    }
                );
            })
    );
});

// If cache is in 'whitelist'
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});