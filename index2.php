<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SIGAM - Login Siswa</title>
    <link rel="icon" type="image/png" href="image/logo-tutwuri-SD.png" />
    
    <meta name="theme-color" content="#3b82f6" /> 
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="SIGAM" />
    
    <link rel="manifest" href="manifest.json" /> 
    <link href="./src/output.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="./node_modules/notyf/notyf.min.css" />
    <script defer src="./assets/vendor/alpine.min.js"></script>
    
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('/WebGami/service-worker.js') 
            .then(function(registration) {
              console.log('ServiceWorker registration successful with scope: ', registration.scope);
            })
            .catch(function(err) {
              console.log('ServiceWorker registration failed: ', err);
            });
        });
      }
    </script>
    
    <style>
      * {
        -webkit-tap-highlight-color: transparent;
      }
      
      body { 
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }
      
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      
      @keyframes pulse-glow {
        0%, 100% { opacity: 0.4; }
        50% { opacity: 0.8; }
      }
      
      .animate-fade-in {
        animation: fadeIn 0.6s ease-out;
      }
      
      .animate-pulse-glow {
        animation: pulse-glow 2s infinite;
      }
      
      @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
          animation-duration: 0.01ms !important;
          animation-iteration-count: 1 !important;
          transition-duration: 0.01ms !important;
        }
      }
    </style>
  </head>
  
  <body x-data="loginApp()" x-init="init()" class="min-h-screen font-['Poppins']">
    <script src="./node_modules/notyf/notyf.min.js"></script>

    <!-- Desktop Layout -->
    <div class="hidden md:flex min-h-screen">
      <!-- Left Hero Section -->
      <div class="flex-1 bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 flex items-center justify-center p-8 relative overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
        
        <div class="text-center text-white max-w-md relative z-10 animate-fade-in">
          <div class="mb-8">
            <div class="relative inline-block">
              <div class="absolute inset-0 bg-white/20 rounded-full blur-xl animate-pulse-glow"></div>
              <img src="image/logo-tutwuri-SD.png" alt="SIGAM Logo" class="relative w-28 h-28 rounded-full shadow-2xl object-cover border-4 border-white/30" />
            </div>
          </div>
          
          <h1 class="text-5xl font-bold mb-4 tracking-tight">SIGAM</h1>
          <p class="text-xl text-blue-100 mb-8">Sistem Informasi Gamifikasi</p>
          
          <div class="grid grid-cols-3 gap-6">
            <div class="text-center group">
              <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center mb-3 mx-auto transition-transform group-hover:scale-110 border border-white/20">
                <i class="fas fa-graduation-cap text-xl"></i>
              </div>
              <p class="text-sm font-medium text-blue-100">Pembelajaran</p>
            </div>
            <div class="text-center group">
              <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center mb-3 mx-auto transition-transform group-hover:scale-110 border border-white/20">
                <i class="fas fa-trophy text-xl"></i>
              </div>
              <p class="text-sm font-medium text-blue-100">Prestasi</p>
            </div>
            <div class="text-center group">
              <div class="w-14 h-14 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center mb-3 mx-auto transition-transform group-hover:scale-110 border border-white/20">
                <i class="fas fa-users text-xl"></i>
              </div>
              <p class="text-sm font-medium text-blue-100">Komunitas</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Right Form Section -->
      <div class="flex-1 bg-gray-50 flex items-center justify-center p-8">
        <div class="w-full max-w-md animate-fade-in">
          <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
            <p class="text-gray-600">Masuk ke akun siswa Anda</p>
          </div>
          
          <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <form action="auth/proses_login_siswa.php" method="POST" class="space-y-6" @submit="handleSubmit">
              <div>
                <label for="nisn" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-id-badge text-blue-500 mr-2"></i>NISN
                </label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="nisn" 
                    name="nisn" 
                    x-model="form.nisn"
                    placeholder="Masukkan NISN Anda" 
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none focus:bg-white transition-all duration-200 text-gray-700"
                    required 
                  />
                  <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <i class="fas fa-check-circle text-green-500 transition-opacity" x-show="form.nisn.length > 8" x-transition.opacity></i>
                  </div>
                </div>
              </div>

              <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-lock text-purple-500 mr-2"></i>Password
                </label>
                <div class="relative">
                  <input 
                    :type="showPassword ? 'text' : 'password'" 
                    id="password" 
                    name="password" 
                    x-model="form.password"
                    placeholder="Masukkan password" 
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none focus:bg-white transition-all duration-200 text-gray-700"
                    required 
                  />
                  <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-purple-500 transition-colors"
                  >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </div>
              </div>

              <input type="hidden" name="kategori" value="siswa">

              <button 
                type="submit" 
                :disabled="loading"
                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none"
              >
                <div x-show="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <i x-show="!loading" class="fas fa-rocket"></i>
                <span x-text="loading ? 'Memproses...' : 'Masuk Sekarang'"></span>
              </button>
            </form>

            <div x-show="showInstallButton" x-transition.opacity class="mt-6">
              <button @click="installPWA()" class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-semibold py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-download mr-2"></i> Instal Aplikasi SIGAM
              </button>
            </div>

            <div class="mt-6 text-center">
              <p class="text-gray-600 text-sm">
                Belum punya akun? 
                <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Daftar di sini</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Layout -->
    <div class="md:hidden min-h-screen bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 relative overflow-hidden">
      <!-- Background decoration -->
      <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.06"%3E%3Ccircle cx="20" cy="20" r="1.5"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
      
      <div class="relative z-10 flex flex-col min-h-screen p-6">
        <!-- Header -->
        <div class="text-center pt-12 pb-8 animate-fade-in">
          <div class="relative inline-block mb-6">
            <div class="absolute inset-0 bg-white/20 rounded-full blur-lg animate-pulse-glow"></div>
            <img src="image/logo-tutwuri-SD.png" alt="SIGAM Logo" class="relative w-20 h-20 rounded-full shadow-xl object-cover border-4 border-white/30" />
          </div>
          <h1 class="text-3xl font-bold text-white mb-2">SIGAM</h1>
          <p class="text-blue-100 text-lg">Selamat Datang</p>
          <p class="text-blue-200 text-sm">Masuk ke akun siswa Anda</p>
        </div>

        <!-- Form Card -->
        <div class="flex-1 flex items-center justify-center">
          <div class="w-full max-w-sm bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-6 border border-white/20 animate-fade-in">
            <form action="auth/proses_login_siswa.php" method="POST" class="space-y-5" @submit="handleSubmit">
              <div>
                <label for="mobile-nisn" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-id-badge text-blue-500 mr-2"></i>NISN
                </label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="mobile-nisn" 
                    name="nisn" 
                    x-model="form.nisn"
                    placeholder="Masukkan NISN Anda" 
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-blue-500 focus:outline-none focus:bg-white transition-all duration-200 text-gray-700"
                    required 
                  />
                  <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <i class="fas fa-check-circle text-green-500 transition-opacity" x-show="form.nisn.length > 8" x-transition.opacity></i>
                  </div>
                </div>
              </div>

              <div x-data="{ showPassword: false }">
                <label for="mobile-password" class="block text-sm font-semibold text-gray-700 mb-2">
                  <i class="fas fa-lock text-purple-500 mr-2"></i>Password
                </label>
                <div class="relative">
                  <input 
                    :type="showPassword ? 'text' : 'password'" 
                    id="mobile-password" 
                    name="password" 
                    x-model="form.password"
                    placeholder="Masukkan password" 
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-purple-500 focus:outline-none focus:bg-white transition-all duration-200 text-gray-700"
                    required 
                  />
                  <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-purple-500 transition-colors"
                  >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </div>
              </div>

              <input type="hidden" name="kategori" value="siswa">

              <button 
                type="submit" 
                :disabled="loading"
                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 active:scale-95 text-white font-semibold py-4 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none mt-6"
              >
                <div x-show="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <i x-show="!loading" class="fas fa-rocket"></i>
                <span x-text="loading ? 'Memproses...' : 'Masuk Sekarang'"></span>
              </button>
            </form>

            <div x-show="showInstallButton" x-transition.opacity class="mt-4">
              <button @click="installPWA()" class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 active:scale-95 text-white font-semibold py-3 rounded-2xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                <i class="fas fa-download mr-2"></i> Instal Aplikasi
              </button>
            </div>

            <div class="mt-4 text-center">
              <p class="text-gray-600 text-sm">
                Belum punya akun? 
                <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Daftar di sini</a>
              </p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="text-center pb-6">
          <p class="text-white/60 text-xs">© 2024 SIGAM. Dikembangkan dengan ❤️</p>
        </div>
      </div>
    </div>
    
    <script>
      // Inisialisasi Notyf
      const notyf = new Notyf({
        duration: 4000,
        position: { x: 'center', y: 'top' },
        dismissible: true,
        types: [
          { 
            type: 'success', 
            background: 'linear-gradient(135deg, #10b981 0%, #3b82f6 100%)', 
            icon: { className: 'fas fa-check-circle', tagName: 'i', color: 'white' } 
          },
          { 
            type: 'error', 
            background: 'linear-gradient(135deg, #ef4444 0%, #f59e0b 100%)', 
            icon: { className: 'fas fa-exclamation-triangle', tagName: 'i', color: 'white' } 
          }
        ]
      });

      // PWA Install
      let deferredPrompt;

      document.addEventListener('alpine:init', () => {
        Alpine.store('appState', {
          showInstallButton: false,
        });

        let pwaInstallNotified = false;
        window.addEventListener('beforeinstallprompt', (e) => {
          e.preventDefault();
          deferredPrompt = e;
          
          if (!pwaInstallNotified) {
            notyf.success('Aplikasi SIGAM dapat diinstal!');
            pwaInstallNotified = true;
          }
          
          Alpine.store('appState').showInstallButton = true;
        });

        window.addEventListener('appinstalled', () => {
          notyf.success('Aplikasi SIGAM berhasil diinstal!');
          Alpine.store('appState').showInstallButton = false;
          deferredPrompt = null;
        });
      });

      // Alpine.js main component
      function loginApp() {
        return {
          loading: false,
          form: {
            nisn: '',
            password: '',
          },
          
          get showInstallButton() {
            return Alpine.store('appState')?.showInstallButton ?? false;
          },

          init() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const success = urlParams.get('success');

            if (error) {
              notyf.error(decodeURIComponent(error));
            }
            if (success) {
              notyf.success(decodeURIComponent(success));
            }
          },

          handleSubmit(event) {
            if (!this.form.nisn.trim() || !this.form.password.trim()) {
              event.preventDefault();
              notyf.error('NISN dan Password tidak boleh kosong!');
              return;
            }
            this.loading = true;
            if (navigator.vibrate) {
              navigator.vibrate(50);
            }
          },

          installPWA() {
            if (deferredPrompt) {
              deferredPrompt.prompt();
              deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                  console.log('User accepted the install prompt');
                } else {
                  notyf.error('Instalasi aplikasi dibatalkan.');
                }
                deferredPrompt = null;
                Alpine.store('appState').showInstallButton = false;
              });
            } else {
              notyf.error('Aplikasi sudah diinstal atau tidak dapat diinstal saat ini.');
            }
          },
        }
      }

      // Keyboard navigation
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
          const form = e.target.closest('form');
          const inputs = Array.from(form.querySelectorAll('input[type="text"], input[type="password"]'));
          const currentIndex = inputs.indexOf(e.target);
          if (currentIndex < inputs.length - 1) {
            e.preventDefault();
            inputs[currentIndex + 1].focus();
          }
        }
      });

      // Online/offline detection
      window.addEventListener('online', () => notyf?.success('Koneksi internet kembali tersedia!'));
      window.addEventListener('offline', () => notyf?.error('Koneksi internet terputus.'));
    </script>
  </body>
</html>