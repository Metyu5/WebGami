<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SIAGAM</title>
    <link rel="icon" type="image/png" href="image/logo-tutwuri-SD.png" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="./src/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      body {
        font-family: 'Poppins', sans-serif;
      }
      .material-icons {
        font-family: 'Material Icons';
        font-weight: normal;
        font-style: normal;
        font-size: 24px; 
        display: inline-block;
        line-height: 1;
        text-transform: none;
        letter-spacing: normal;
        word-wrap: normal;
        white-space: nowrap;
        direction: ltr;
        -webkit-font-smoothing: antialiased;
        text-rendering: optimizeLegibility;
        -moz-osx-font-smoothing: grayscale;
        font-feature-settings: 'liga';
      }

      /* Fade-in class */
      .fade-in {
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
      }

      .fade-in.visible {
        opacity: 1;
      }
    </style>
  </head>
  <body class="bg-gradient-to-r from-blue-200 via-pink-100 to-yellow-100 min-h-screen flex items-center justify-center p-4">

    <div id="form-container" class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 space-y-6 border-[6px] border-red-300 fade-in">
      <!-- Logo Section -->
      <div class="flex justify-center mb-6">
        <img
          src="image/logo-tutwuri-SD.png"
          alt="Logo Gamifikasi SD"
          class="w-28 h-28 rounded-full border-4 border-blue-400 shadow-lg object-cover"
        
        />
      </div>

      <h2 class="text-3xl font-extrabold text-center text-blue-700 mb-4">Login - SIGAM </h2>

      <form action="auth/proses_login.php" method="POST" class="space-y-4"> 
        <!-- Email -->
        <div>
          <label for="email" class="block mb-1 text-sm font-semibold text-gray-700">Email</label>
          <div class="flex items-center border-2 border-blue-200 rounded-xl px-3 py-2 bg-blue-50 shadow-inner">
            <span class="material-icons text-blue-500 text-lg mr-2">email</span>
            <input
              type="email"
              id="email"
              name="email"
              placeholder="Masukan email anda"
              class="w-full bg-transparent outline-none text-sm placeholder:text-gray-400"
              required
            />
          </div>
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
          <label for="password" class="block mb-1 text-sm font-semibold text-gray-700">Password</label>
          <div class="flex items-center border-2 border-yellow-200 rounded-xl px-3 py-2 bg-yellow-50 shadow-inner">
            <span class="material-icons text-yellow-500 text-lg mr-2">lock</span>
            <input
              :type="showPassword ? 'text' : 'password'"
              id="password"
              placeholder="*******"
              name="password"
              class="w-full bg-transparent outline-none text-sm placeholder:text-gray-400"
              required
            />
            <button type="button" @click="showPassword = !showPassword" class="ml-2 text-gray-500 focus:outline-none">
              <span x-show="!showPassword" class="material-icons">visibility_off</span> <!-- Closed eye icon -->
              <span x-show="showPassword" class="material-icons">visibility</span> <!-- Open eye icon -->
            </button>
          </div>
        </div>

        <!-- Konfirmasi Password -->
        <div x-data="{ showConfirmPassword: false }">
          <label for="confirm-password" class="block mb-1 text-sm font-semibold text-gray-700">Konfirmasi Password</label>
          <div class="flex items-center border-2 border-green-200 rounded-xl px-3 py-2 bg-green-50 shadow-inner">
            <span class="material-icons text-green-500 text-lg mr-2">check_circle</span>
            <input
              :type="showConfirmPassword ? 'text' : 'password'"
              id="confirm-password"
              placeholder="*******"
              name="confirm-password"
              class="w-full bg-transparent outline-none text-sm placeholder:text-gray-400"
              required
            />
            <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="ml-2 text-gray-500 focus:outline-none">
              <span x-show="!showConfirmPassword" class="material-icons">visibility_off</span> <!-- Closed eye icon -->
              <span x-show="showConfirmPassword" class="material-icons">visibility</span> <!-- Open eye icon -->
            </button>
          </div>
        </div>

        <!-- Dropdown Role -->
        <div x-data="{ open: false, selected: '👶 Pilih Peran', kategori: '' }" class="relative">
          <label class="block mb-1 text-sm font-semibold text-blue-700">Peran</label>
          
          <!-- Dropdown Button -->
          <button
            @click="open = !open"
            type="button"
            class="w-full border-2 border-purple-200 rounded-xl px-3 py-2 bg-purple-50 text-left flex justify-between items-center shadow-inner"
          >
            <span x-text="selected" class="text-sm text-blue-600"></span>
            <span class="material-icons text-blue-500 ml-2">arrow_drop_down</span>
          </button>

          <!-- Hidden Input for Form Submission -->
          <input type="hidden" name="kategori" x-bind:value="kategori" required />


          <!-- Dropdown Items -->
          <div
            x-show="open"
            @click.away="open = false"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-10 mt-1 w-full bg-white rounded-xl shadow-lg border border-gray-200"
          >
            <ul class="text-sm text-blue-700 py-1">
              <li @click="selected = '👨‍💼 Administrator'; kategori = 'admin'; open = false" class="px-4 py-2 hover:bg-blue-100 cursor-pointer rounded-lg">
                <span class="material-icons align-middle text-blue-700 mr-2">admin_panel_settings</span> Administrator
              </li>
              <li @click="selected = '👩‍🏫 Wali Kelas'; kategori = 'wali kelas'; open = false" class="px-4 py-2 hover:bg-blue-100 cursor-pointer rounded-lg">
                <span class="material-icons align-middle text-blue-700 mr-2">school</span> Wali Kelas
              </li>
              <li @click="selected = '👦 Siswa'; kategori = 'siswa'; open = false" class="px-4 py-2 hover:bg-blue-100 cursor-pointer rounded-lg">
                <span class="material-icons align-middle text-blue-700 mr-2">person</span> Siswa
              </li>
            </ul>
          </div>
        </div>

        <!-- Tombol Submit -->
        <div>
          <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-3 rounded-xl transition duration-300 shadow-md transform hover:scale-105">
            Masuk Sekarang 🚀
          </button>
        </div>
      </form>
    </div>

  </body>

  <script>
    // Menampilkan pesan error atau success dari proses login
    <?php if (isset($_GET['error'])): ?>
        const errorMessage = "<?php echo $_GET['error']; ?>";
        if (errorMessage === "input_kosong") {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Mohon Cek Kembali Email Dan Password Anda!',
            });
        } else if (errorMessage === "login_gagal") {
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: 'Email atau password salah!',
            });
        } else if (errorMessage === "kategori_salah") {
            Swal.fire({
                icon: 'error',
                title: 'Peran Salah',
                text: 'Peran yang Anda pilih tidak sesuai dengan data pengguna.',
            });
        } else if (errorMessage === "kategori_tidak_dikenal") {
            Swal.fire({
                icon: 'error',
                title: 'Kategori Tidak Dikenal',
                text: 'Kategori pengguna tidak dikenal.',
            });
        } else if (errorMessage === "password_mismatch") {
            Swal.fire({
                icon: 'error',
                title: 'Password Tidak Cocok',
                text: 'Password dan konfirmasi password tidak cocok.',
            });
        }
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        const successMessage = "<?php echo $_GET['success']; ?>";
        Swal.fire({
            icon: 'success',
            title: 'Selamat Datang',
            text: successMessage,
        });
    <?php endif; ?>

    // Menambahkan efek fade-in setelah halaman dimuat
    window.onload = function() {
      document.getElementById('form-container').classList.add('visible');
    };
  </script>
</html>
