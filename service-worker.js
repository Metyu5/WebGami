// Nama cache Anda. Ubah ini jika Anda melakukan perubahan signifikan pada aset yang di-cache
const CACHE_NAME = 'sigam-cache-v2'; // Ubah versi cache untuk memastikan service worker baru diinstal

// Daftar URL aset yang ingin Anda cache agar tersedia secara offline
const urlsToCache = [
  '/WebGami/', // Halaman root proyek Anda
  '/WebGami/index2.php', // Halaman login Anda
  '/WebGami/src/output.css', // CSS Tailwind Anda
  '/WebGami/image/logo-192.png',
  '/WebGami/image/logo-512.png', // Pastikan file ini ada!
  '/WebGami/image/logo-tutwuri-SD.png', // Jika ini juga digunakan di halaman login
  
  // Tambahkan Notyf dan Alpine.js ke cache
  '/WebGami/node_modules/notyf/notyf.min.js', // Tambahkan Notyf JS
  '/WebGami/node_modules/notyf/notyf.min.css', // Tambahkan Notyf CSS jika Anda menggunakannya
  '/WebGami/node_modules/alpinejs/dist/cdn.min.js', // Tambahkan Alpine.js

  // Tambahkan URL aset penting lainnya yang membuat aplikasi Anda berfungsi offline
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css',
  'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap'
];

// Event 'install': Dipicu saat service worker pertama kali diinstal
self.addEventListener('install', event => {
  console.log('Service Worker: Installing...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Service Worker: Caching app shell');
        return cache.addAll(urlsToCache);
      })
      .catch(err => console.error('Service Worker: Cache addAll failed', err))
  );
});

// Event 'fetch': Dipicu setiap kali browser meminta resource
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Jika resource ada di cache, kembalikan dari cache
        if (response) {
          return response;
        }
        // Jika tidak ada di cache, ambil dari jaringan
        return fetch(event.request)
          .then(networkResponse => {
            // Tambahkan resource yang baru diambil ke cache untuk penggunaan di masa mendatang
            return caches.open(CACHE_NAME).then(cache => {
              // Hanya cache respons yang valid (status 200) dan bukan permintaan rentang (range requests)
              if (networkResponse.ok && event.request.method === 'GET' && !event.request.url.includes('chrome-extension')) {
                // Pastikan tidak ada duplikasi atau kesalahan pada resource yang di-cache
                cache.put(event.request, networkResponse.clone());
              }
              return networkResponse;
            });
          })
          .catch(() => {
            // Ini akan dipicu jika fetch gagal (misal: offline dan tidak ada di cache)
            console.log('Service Worker: Fetch failed for', event.request.url);
            // Anda bisa mengembalikan halaman offline khusus di sini, misalnya:
            // return caches.match('/WebGami/offline.html'); 
            // Untuk sementara, kita tidak mengembalikan apapun jika offline dan tidak ada di cache
          });
      })
  );
});

// Event 'activate': Dipicu saat service worker baru aktif dan yang lama diganti
self.addEventListener('activate', event => {
  console.log('Service Worker: Activating...');
  // Hapus cache lama yang tidak lagi dibutuhkan
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheWhitelist.indexOf(cacheName) === -1) {
            console.log('Service Worker: Deleting old cache', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  // Klaim klien agar service worker mengontrol halaman segera setelah aktivasi
  return self.clients.claim();
});