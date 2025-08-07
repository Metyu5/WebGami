<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SIGAM - Login Siswa</title>
    <link rel="icon" type="image/png" href="image/logo-tutwuri-SD.png" />
    
    <meta name="theme-color" content="#667eea" /> 
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="SIGAM" />
    
    <link rel="manifest" href="manifest.json" /> 
    <link href="./src/output.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="./node_modules/@fortawesome/fontawesome-free/css/all.min.css" />
    
    <link rel="stylesheet" href="./node_modules/notyf/notyf.min.css">
    
    <script src="./node_modules/alpinejs/dist/cdn.min.js" defer></script>
    
    <script>
      if ('serviceWorker' in navigator) {
          window.addEventListener('load', function() {
              // Path ke service worker harus relatif terhadap root domain web server
              // Contoh: '/WebGami/service-worker.js' jika proyek WebGami ada di root server
              // ATAU 'service-worker.js' jika file index2.php dan service-worker.js ada di folder yang SAMA (WebGami)
              // Saya asumsikan `/WebGami/service-worker.js` karena Anda sudah menggunakannya sebelumnya.
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
      /* Variabel CSS tetap sama */
      :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --glass-bg: rgba(255, 255, 255, 0.25);
        --glass-border: rgba(255, 255, 255, 0.18);
        --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        --shadow-heavy: 0 15px 35px rgba(0, 0, 0, 0.1);
      }
      
      * {
        -webkit-tap-highlight-color: transparent;
      }
      
      body { 
        /* Ubah font-family dari 'Inter' menjadi 'Poppins' */
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--primary-gradient);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }
      
      /* Glass Morphism Effects */
      .glass-morphism {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        box-shadow: var(--shadow-light);
      }
      
      .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
      }
      
      /* Interactive Elements */
      .interactive-input {
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
      }
      
      .interactive-input::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
      }
      
      .interactive-input:focus {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.15);
        border-color: #667eea;
      }
      
      .interactive-input:focus::before {
        left: 100%;
      }
      
      /* Button Animations */
      .btn-primary {
        background: var(--primary-gradient);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        overflow: hidden;
      }
      
      .btn-primary::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
      }
      
      .btn-primary:hover::before {
        width: 300px;
        height: 300px;
      }
      
      .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
      }
      
      .btn-primary:active {
        transform: translateY(-1px);
      }
      
      /* Icon Animations */
      .icon-bounce {
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      }
      
      .icon-bounce:hover {
        transform: scale(1.2) rotate(5deg);
      }
      
      .icon-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
      }
      
      @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
      }
      
      /* Floating Elements */
      .float-animation {
        animation: float 6s ease-in-out infinite;
      }
      
      @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        33% { transform: translateY(-10px) rotate(1deg); }
        66% { transform: translateY(-5px) rotate(-1deg); }
      }
      
      /* Slide Animations */
      .slide-up {
        animation: slideUp 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);
      }
      
      @keyframes slideUp {
        from {
          opacity: 0;
          transform: translateY(50px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      /* Responsive Adaptations */
      @media (min-width: 768px) {
        .desktop-layout {
          display: grid;
          grid-template-columns: 1fr 1fr;
          min-height: 100vh;
        }
        
        .desktop-hero {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          display: flex;
          align-items: center;
          justify-content: center;
          position: relative;
          overflow: hidden;
        }
        
        .desktop-form {
          background: #ffffff;
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 2rem;
        }
      }
      
      @media (max-width: 767px) {
        .mobile-layout {
          background: var(--primary-gradient);
          min-height: 100vh;
        }
      }
      
      /* iOS Specific Styles */
      @supports (-webkit-backdrop-filter: blur(10px)) {
        .ios-blur {
          -webkit-backdrop-filter: blur(25px);
          backdrop-filter: blur(25px);
        }
      }
      
      /* Loading Animation */
      .loading-spinner {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s ease-in-out infinite;
      }
      
      @keyframes spin {
        to { transform: rotate(360deg); }
      }
      
      /* Custom Scrollbar */
      ::-webkit-scrollbar {
        width: 6px;
      }
      
      ::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
      }
      
      ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
      }
      
      /* Notyf Custom Styles */
      .notyf__toast--success {
        background: var(--success-gradient) !important;
      }
      
      .notyf__toast--error {
        background: var(--secondary-gradient) !important;
      }
      
      /* Focus Indicators */
      .focus-ring:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
      }
      
      /* Optimalisasi untuk Reduced Motion */
      body.reduced-motion .float-animation,
      body.reduced-motion .icon-bounce,
      body.reduced-motion .icon-pulse,
      body.reduced-motion .animate-pulse {
        animation: none !important;
        transition: none !important;
        transform: none !important;
      }
    </style>
  </head>
  
  <body x-data="loginApp()" x-init="init()">
    <script src="./node_modules/notyf/notyf.min.js"></script> 

    <div class="hidden md:block">
      <div class="desktop-layout">
        <div class="desktop-hero">
          <div class="text-center text-white z-10 relative">
            <div class="mb-8">
              <div class="inline-block relative">
                <div class="absolute inset-0 bg-white rounded-full blur-xl opacity-20 animate-pulse"></div>
                <img src="image/logo-tutwuri-SD.png" alt="SIGAM Logo" class="relative w-32 h-32 rounded-full shadow-2xl float-animation border-4 border-white/30 object-cover" />
              </div>
            </div>
            <h1 class="text-5xl font-black mb-4 tracking-tight">SIGAM</h1>
            <p class="text-xl font-medium opacity-90 mb-8">Sistem Informasi Gamifikasi</p>
            <div class="flex justify-center space-x-8">
              <div class="text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-3 mx-auto icon-bounce">
                  <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
                <p class="text-sm font-medium">Pembelajaran</p>
              </div>
              <div class="text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-3 mx-auto icon-bounce">
                  <i class="fas fa-trophy text-2xl"></i>
                </div>
                <p class="text-sm font-medium">Prestasi</p>
              </div>
              <div class="text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-3 mx-auto icon-bounce">
                  <i class="fas fa-users text-2xl"></i>
                </div>
                <p class="text-sm font-medium">Komunitas</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="desktop-form">
          <div class="w-full max-w-md slide-up">
            <div class="text-center mb-8">
              <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang Kembali</h2>
              <p class="text-gray-600">Masuk ke akun siswa Anda</p>
            </div>
            
            <form action="auth/proses_login_siswa.php" method="POST" class="space-y-6" @submit="handleSubmit">
              <div class="space-y-2">
                <label for="nisn" class="block text-sm font-semibold text-gray-700">
                  <i class="fas fa-id-badge text-blue-500 mr-2"></i>NISN
                </label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="nisn" 
                    name="nisn" 
                    x-model="form.nisn"
                    placeholder="Masukkan NISN Anda" 
                    class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none interactive-input text-gray-700 placeholder-gray-400 font-medium focus-ring"
                    required 
                  />
                  <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <i class="fas fa-user-check text-gray-400 icon-pulse" x-show="form.nisn.length > 0"></i>
                  </div>
                </div>
              </div>

              <div class="space-y-2" x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-semibold text-gray-700">
                  <i class="fas fa-lock text-purple-500 mr-2"></i>Password
                </label>
                <div class="relative">
                  <input 
                    :type="showPassword ? 'text' : 'password'" 
                    id="password" 
                    name="password" 
                    x-model="form.password"
                    placeholder="Masukkan password" 
                    class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-purple-500 focus:outline-none interactive-input text-gray-700 placeholder-gray-400 font-medium focus-ring"
                    required 
                  />
                  <button 
                    type="button" 
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-purple-500 transition-colors icon-bounce"
                  >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </div>
              </div>

              <input type="hidden" name="kategori" value="siswa">

              <button 
                type="submit" 
                :disabled="loading"
                class="w-full btn-primary text-white font-bold py-4 rounded-xl shadow-xl flex items-center justify-center space-x-3 disabled:opacity-70 disabled:cursor-not-allowed relative z-10"
              >
                <div x-show="loading" class="loading-spinner"></div>
                <i x-show="!loading" class="fas fa-rocket"></i>
                <span x-text="loading ? 'Memproses...' : 'Masuk Sekarang'"></span>
              </button>
            </form>

            <div x-show="showInstallButton" x-transition.opacity class="text-center mt-6">
                <button @click="installPWA()" class="btn-primary text-white font-semibold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                    <i class="fas fa-download mr-2"></i> Instal Aplikasi SIGAM
                </button>
            </div>

            <div class="mt-8 text-center">
              <p class="text-gray-600 text-sm">
                Belum punya akun? 
                <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold transition-colors">Daftar di sini</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="md:hidden mobile-layout min-h-screen flex flex-col">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-white opacity-10 rounded-full mix-blend-multiply filter blur-xl animate-pulse"></div>
        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-purple-300 opacity-10 rounded-full mix-blend-multiply filter blur-xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/3 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-300 opacity-5 rounded-full mix-blend-multiply filter blur-2xl"></div>
      </div>

      <div class="flex-1 flex flex-col justify-center px-6 py-12 relative z-10">
        <div class="text-center mb-12">
          <div class="relative inline-block mb-6">
            <div class="absolute inset-0 bg-white rounded-full blur-lg opacity-30 animate-pulse"></div>
            <img src="image/logo-tutwuri-SD.png" alt="SIGAM Logo" class="relative w-24 h-24 mx-auto rounded-full shadow-2xl float-animation object-cover border-4 border-white/30" />
          </div>
          <h1 class="text-3xl font-black text-white mb-2 tracking-tight">SIGAM</h1>
          <p class="text-white/90 text-lg font-medium mb-2">Selamat Datang</p>
          <p class="text-white/70 text-sm">Masuk ke akun siswa Anda</p>
        </div>

        <div class="glass-card rounded-3xl p-6 mx-auto w-full max-w-sm slide-up ios-blur">
          <form action="auth/proses_login_siswa.php" method="POST" class="space-y-5" @submit="handleSubmit">
            <div class="space-y-2">
              <label for="mobile-nisn" class="block text-sm font-semibold text-gray-700 flex items-center">
                <i class="fas fa-id-badge text-blue-500 mr-2 icon-bounce"></i>NISN
              </label>
              <div class="relative">
                <input 
                  type="text" 
                  id="mobile-nisn" 
                  name="nisn" 
                  x-model="form.nisn"
                  placeholder="Masukkan NISN Anda" 
                  class="w-full px-4 py-4 bg-white/80 border-2 border-gray-200 rounded-2xl focus:border-blue-500 focus:outline-none interactive-input text-gray-700 placeholder-gray-400 font-medium shadow-sm focus-ring"
                  required 
                />
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                  <i class="fas fa-check-circle text-green-500 icon-pulse" x-show="form.nisn.length > 8" x-transition></i>
                </div>
              </div>
            </div>

            <div class="space-y-2" x-data="{ showPassword: false }">
              <label for="mobile-password" class="block text-sm font-semibold text-gray-700 flex items-center">
                <i class="fas fa-lock text-purple-500 mr-2 icon-bounce"></i>Password
              </label>
              <div class="relative">
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  id="mobile-password" 
                  name="password" 
                  x-model="form.password"
                  placeholder="Masukkan password" 
                  class="w-full px-4 py-4 bg-white/80 border-2 border-gray-200 rounded-2xl focus:border-purple-500 focus:outline-none interactive-input text-gray-700 placeholder-gray-400 font-medium shadow-sm focus-ring"
                  required 
                />
                <button 
                  type="button" 
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-purple-500 transition-colors icon-bounce"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
            </div>

            <input type="hidden" name="kategori" value="siswa">

            <button 
              type="submit" 
              :disabled="loading"
              class="w-full btn-primary text-white font-bold py-4 rounded-2xl shadow-xl flex items-center justify-center space-x-3 disabled:opacity-70 disabled:cursor-not-allowed mt-8 relative z-10"
            >
              <div x-show="loading" class="loading-spinner"></div>
              <i x-show="!loading" class="fas fa-rocket"></i>
              <span x-text="loading ? 'Memproses...' : 'Masuk Sekarang'"></span>
            </button>
          </form>

          <div x-show="showInstallButton" x-transition.opacity class="text-center mt-6">
              <button @click="installPWA()" class="w-full btn-primary text-white font-semibold py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
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

        <div class="text-center mt-8">
          <p class="text-white/60 text-xs font-medium">© 2024 SIGAM. Dikembangkan dengan ❤️</p>
        </div>
      </div>
    </div>
    
    <script>
      // Inisialisasi Notyf
      const notyf = new Notyf({
        duration: 5000,
        position: { x: 'center', y: 'top' },
        dismissible: true,
        types: [
          { type: 'success', background: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', icon: { className: 'fas fa-check-circle', tagName: 'i', color: 'white' } },
          { type: 'error', background: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', icon: { className: 'fas fa-exclamation-triangle', tagName: 'i', color: 'white' } }
        ]
      });

      // Variabel global untuk menyimpan event beforeinstallprompt
      let deferredPrompt;
      let pwaInstallNotified = false; // Flag untuk memastikan notifikasi PWA hanya muncul sekali per sesi

      // Listener untuk event beforeinstallprompt
      window.addEventListener('beforeinstallprompt', (e) => {
        // Mencegah mini-infobar default muncul di mobile
        e.preventDefault();
        // Simpan event sehingga bisa dipicu nanti
        deferredPrompt = e;
        console.log('beforeinstallprompt event fired! deferredPrompt saved.');

        // Tampilkan notifikasi Notyf bahwa PWA bisa diinstal, jika belum pernah ditunjukkan
        if (!pwaInstallNotified) {
          notyf.success('Aplikasi SIGAM Dapat Di Install.');
          pwaInstallNotified = true; // Set flag agar tidak muncul lagi
        }

        // Tampilkan tombol instal kustom di UI (via Alpine.js)
        Alpine.store('loginApp').showInstallButton = true;
      });

      // Listener untuk event appinstalled
      window.addEventListener('appinstalled', () => {
        console.log('PWA was successfully installed!');
        notyf.success('Aplikasi SIGAM berhasil diinstal!');
        // Sembunyikan tombol instal setelah terinstal
        Alpine.store('loginApp').showInstallButton = false;
        deferredPrompt = null; // Hapus deferredPrompt karena sudah terpakai
      });

      // Alpine.js main component
      function loginApp() {
        return {
          loading: false,
          form: {
            nisn: '',
            password: '',
          },
          showInstallButton: false, // State untuk mengontrol visibilitas tombol install

          init() {
            // Tangani notifikasi dari URL (login gagal/berhasil)
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const success = urlParams.get('success');
            
            if (error) {
              notyf.error(decodeURIComponent(error));
              // Opsional: Hapus parameter URL agar notifikasi tidak muncul lagi saat refresh
              // history.replaceState(null, '', window.location.pathname);
            }
            
            if (success) {
              notyf.success(decodeURIComponent(success));
              // Opsional: Hapus parameter URL
              // history.replaceState(null, '', window.location.pathname);
            }

            // Inisialisasi state tombol install berdasarkan deferredPrompt yang mungkin sudah ada
            if (deferredPrompt && !pwaInstallNotified) { // Cek juga pwaInstallNotified agar notif tidak double saat init
              this.showInstallButton = true;
              notyf.success('Aplikasi SIGAM dapat diinstal! Klik tombol "Instal Aplikasi" di bawah untuk menambahkan ke perangkat Anda.');
              pwaInstallNotified = true;
            }
          },
          
          handleSubmit(event) {
            console.log('--- DEBUG HANDLE SUBMIT ---');
            console.log('Nilai form.nisn:', this.form.nisn, 'Length:', this.form.nisn.length);
            console.log('Nilai form.password:', this.form.password, 'Length:', this.form.password.length);
            
            if (!this.form.nisn.trim()) {
              event.preventDefault();
              notyf.error('NISN tidak boleh kosong!');
              this.loading = false; // Pastikan loading direset jika validasi gagal
              return;
            }
            
            if (!this.form.password.trim()) {
              event.preventDefault();
              notyf.error('Password tidak boleh kosong!');
              this.loading = false; // Pastikan loading direset jika validasi gagal
              return;
            }
            
            this.loading = true;
            
            if (navigator.vibrate) {
              navigator.vibrate(50);
            }
            // Form akan disubmit secara alami jika validasi lolos
          },

          // Fungsi untuk memicu prompt instalasi PWA
          installPWA() {
            console.log('Attempting to install PWA...');
            if (deferredPrompt) {
              // Tampilkan prompt instalasi
              deferredPrompt.prompt();
              // Tunggu respon pengguna terhadap prompt
              deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                  console.log('User accepted the A2HS prompt');
                  // notyf.success('Instalasi aplikasi dimulai...'); // Notifikasi 'berhasil diinstal' akan muncul dari appinstalled event
                } else {
                  console.log('User dismissed the A2HS prompt');
                  notyf.error('Instalasi aplikasi dibatalkan.');
                }
                // Bersihkan deferredPrompt karena hanya bisa digunakan sekali
                deferredPrompt = null;
                this.showInstallButton = false; // Sembunyikan tombol setelah prompt tampil (diterima/ditolak)
              });
            } else {
              notyf.error('Aplikasi sudah diinstal atau tidak dapat diinstal saat ini.');
              console.log('deferredPrompt is null, cannot install PWA.');
            }
          },
        }
      }
      
      // Add touch gesture support
      let touchStartX = 0;
      let touchStartY = 0;
      
      document.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
      }, { passive: true });
      
      document.addEventListener('touchmove', (e) => {
        if (!touchStartX || !touchStartY) return;
        
        let touchEndX = e.touches[0].clientX;
        let touchEndY = e.touches[0].clientY;
        
        let diffX = touchStartX - touchEndX;
        let diffY = touchStartY - touchEndY;
        
        // Add subtle parallax effect on mobile - KECEPATAN DIKURANGI UNTUK PERFORMA
        if (window.innerWidth < 768) {
          const parallaxElements = document.querySelectorAll('.float-animation');
          parallaxElements.forEach(el => {
            const speed = 0.05; // Kurangi kecepatan untuk mengurangi beban
            el.style.transform = `translateX(${diffX * speed}px) translateY(${diffY * speed}px)`;
          });
        }
      }, { passive: true });
      
      // Performance optimizations using requestIdleCallback
      if ('requestIdleCallback' in window) {
        requestIdleCallback(() => {
          console.log('App optimized for performance');
        });
      }
      
      // Add device-specific optimizations
      const userAgent = navigator.userAgent.toLowerCase();
      const isIOS = /iphone|ipad|ipod/.test(userAgent);
      const isAndroid = /android/.test(userAgent);
      
      if (isIOS) {
        document.body.classList.add('ios-device');
      }
      
      if (isAndroid) {
        document.body.classList.add('android-device');
        if (window.chrome) {
          document.body.classList.add('chrome-browser');
        }
      }
      
      // Add connection quality detection - ubah logika agar lebih spesifik
      if ('connection' in navigator) {
        const connection = navigator.connection;
        // Hanya tambahkan kelas jika koneksi lambat, agar tidak memengaruhi yang cepat
        if (connection.effectiveType && (connection.effectiveType.includes('2g') || connection.effectiveType.includes('3g'))) {
            document.body.classList.add('reduced-motion');
        }
      }
      
      // Add dark mode detection
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.body.classList.add('dark-mode-preferred');
      }
      
      // Add high contrast mode detection
      if (window.matchMedia && window.matchMedia('(prefers-contrast: high)').matches) {
        document.body.classList.add('high-contrast-mode');
      }
      
      // Add reduced motion preference detection
      if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.body.classList.add('reduced-motion');
      }
      
      // Add battery API support
      if ('getBattery' in navigator) {
        navigator.getBattery().then((battery) => {
          // Hanya tambahkan kelas jika baterai benar-benar rendah dan tidak sedang mengisi daya
          if (battery.level < 0.2 && !battery.charging) { 
            document.body.classList.add('power-save-mode');
          }
        });
      }
      
      // Add orientation change handling
      window.addEventListener('orientationchange', () => {
        setTimeout(() => {
          document.body.style.display = 'none';
          document.body.offsetHeight; 
          document.body.style.display = '';
        }, 100);
      });
      
      // Add focus management for accessibility
      document.addEventListener('DOMContentLoaded', () => {
        const firstInput = document.querySelector('input[name="nisn"]');
        if (firstInput && window.innerWidth > 768) {
          firstInput.focus();
        }
      });
      
      // Add keyboard navigation support
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
          const form = e.target.closest('form');
          const inputs = Array.from(form.querySelectorAll('input[type="text"], input[type="password"]'));
          const currentIndex = inputs.indexOf(e.target);
          
          if (currentIndex < inputs.length - 1) {
            e.preventDefault();
            inputs[currentIndex + 1].focus();
          } else {
            form.submit();
          }
        }
      });
      
      // Add error recovery with better error handling
      window.addEventListener('error', (e) => {
        console.error('Application error:', e.error);
        if (e.error && e.error.message && !e.error.message.includes('notyf')) {
          if (window.notyf) {
            notyf.error('Terjadi kesalahan sistem. Silakan refresh halaman.');
          }
        }
      });
      
      // Add online/offline detection
      window.addEventListener('online', () => {
        if (window.notyf) {
          notyf.success('Koneksi internet kembali tersedia!');
        }
      });
      
      window.addEventListener('offline', () => {
        if (window.notyf) {
          notyf.error('Koneksi internet terputus. Beberapa fitur mungkin tidak berfungsi.');
        }
      });
    </script>
  </body>
</html>