var appVersion = 'nothi-next';

// files

var files = [
    '/nothi-next/mertonic/assets/css/style.bundle.css',
    '/nothi-next/dashboard',
    '/nothi-next/daak/inbox',
]

//install

self.addEventListener('install', event => {

    event.waitUntill(
        caches.open(appVersion)
        .then(cache => {
            return cache.addAll(files)
            .catch( err => {
                console.error('Error caching', err);
            })
        })
    )  
    console.log('Service worker installed');
    self.skipwaiting();
})


self.addEventListener('activate', event => {
    event.waitUntill(
        caches.keys()
        .then(
            cachesNames =>{
                return Promise.all(
                    cachesNames.map(cache =>{
                        if(cache !== appVersion){
                            return caches.delete(cache);
                        }
                    })
                )
            }
        )
    )
    self.clients.claim();
})

self.addEventListener('fetch', event=>{
    console.log('fetch event', event);
    event.respondWith(
        caches.match(event.request)
        .then(res => {
            return res || fetch(event.request);
        }).catch(err => {
            console.log('err', err);
        })
    )
})

