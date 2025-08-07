<?php
session_start();

if (!isset($_SESSION['siswaId'])) {
    header("Location: ../index2.php?error=Anda harus login untuk mengakses dashboard.");
    exit();
}

$username_siswa = 'Pengguna';
$foto_siswa = '../image/default_avatar.png'; // Path default jika tidak ada foto di sesi

if (isset($_SESSION['username']) && isset($_SESSION['foto']) && !empty($_SESSION['foto'])) {
    $username_siswa = htmlspecialchars($_SESSION['username']);
    // Path di $_SESSION['foto'] seharusnya sudah 'upload/profile/nama_file_unik.png'
    // Jadi kita hanya perlu menambahkan '../' di depannya karena dashboard.php ada di 'siswa/pages/'
    // Dan tambahkan ?time() untuk mencegah cache.
    $foto_siswa = '../' . htmlspecialchars($_SESSION['foto']) . '?' . time();
}
// Jika $_SESSION['foto'] tidak ada atau kosong, maka akan tetap menggunakan default_avatar.png
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MathQuest</title>
    <link href="../src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
        }
        .scroll-hidden::-webkit-scrollbar {
            display: none;
        }
        .scroll-hidden {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .category-card {
            min-width: 120px;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .category-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
        .game-card {
            background: linear-gradient(135deg, #ffffff 0%, #f9f9f9 100%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .game-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            min-width: 140px;
            z-index: 50;
            transform-origin: top right;
        }
        @media (max-width: 767px) {
            .profile-dropdown-desktop {
                display: none;
            }
        }
        @media (min-width: 768px) {
            .profile-dropdown-mobile {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-blue-50">
    <div id="loadingOverlay" class="fixed inset-0 bg-white bg-opacity-20 flex flex-col items-center justify-center z-[9999] hidden">
        <div>
            <img src="../image/logo-192.png" alt="Memuat..." class="h-16 w-16 mb-4 animate-bounce">
        </div>

        <div class="flex flex-col items-center mt-8">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-red-500 mb-2"></div>
            <p class="text-red-700 text-sm font-semibold">Memuat...</p>
        </div>
    </div>
    <div class="min-h-screen flex flex-col">
        <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 shadow-lg flex items-center justify-between">
            <div class="flex items-center">
                <img src="../image/logo-tutwuri-SD.png" alt="Logo MathQuest" class="h-10 w-10 rounded-full mr-3 shadow-md">
                <h1 class="text-2xl font-bold tracking-tight">MathQuest</h1>
            </div>

            <div x-data="{ open: false }" @click.outside="open = false" class="relative z-50">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-400 rounded-full">
                    <img src="<?php echo $foto_siswa; ?>" alt="Avatar Pengguna" class="h-10 w-10 rounded-full border-2 border-white shadow-md object-cover cursor-pointer">
                </button>

                <div x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 transform"
                    x-transition:enter-end="opacity-100 scale-100 transform"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 transform"
                    x-transition:leave-end="opacity-0 scale-95 transform"
                    class="absolute right-0 mt-2 bg-white rounded-lg shadow-xl py-1 dropdown-menu"
                    style="display: none;"
                >
                    <div class="px-4 py-2 text-sm text-gray-700 font-semibold border-b border-gray-100">
                        Halo, <?php echo $username_siswa; ?>!
                    </div>
                    <a href="../siswa/content/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center" id="profileLinkHeader">
                        <i class="fas fa-user-circle mr-2"></i> Lihat Profil
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                        <i class="fas fa-cog mr-2"></i> Pengaturan
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="../auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 pb-20">
            <section class="text-center mb-6">
                <p class="text-xl font-semibold text-gray-800">
                    <span class="text-blue-700">Yuk, Petualangan Matematika</span> Seru Dimulai! 🚀
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-lg font-bold text-gray-800 mb-3">Pilih Kelas / Topik</h2>
                <div class="flex overflow-x-auto space-x-4 pb-2 scroll-hidden">
                    <div class="flex-shrink-0 bg-white rounded-xl shadow-md p-3 text-center category-card">
                        <img src="./assets/images/mat-vol-1-kelas-3.png" alt="Kelas 3 SD" class="mx-auto h-15 w-15 mb-1 rounded-full object-cover">
                        <p class="text-sm font-medium text-gray-700">Kelas 3 SD</p>
                    </div>
                    <div class="flex-shrink-0 bg-white rounded-xl shadow-md p-3 text-center category-card">
                        <img src="./assets/images/I1MD2MAT04DG1.jpg" alt="Kelas 4 SD" class="mx-auto h-15 w-15 mb-1 rounded-full object-cover">
                        <p class="text-sm font-medium text-gray-700">Kelas 4 SD</p>
                    </div>
                    <div class="flex-shrink-0 bg-white rounded-xl shadow-md p-3 text-center category-card">
                        <img src="./assets/images/Matematika-Vol-1-BS-KLS-V-cover.png" alt="Kelas 5 SD" class="mx-auto h-15 w-15 mb-1 rounded-full object-cover">
                        <p class="text-sm font-medium text-gray-700">Kelas 5 SD</p>
                    </div>
                    <div class="flex-shrink-0 bg-white rounded-xl shadow-md p-3 text-center category-card">
                        <img src="./assets/images/Poster_Pintar-Penjumlahan_1702965760.jpg" alt="Penjumlahan" class="mx-auto h-15 w-15 mb-1 rounded-full object-cover">
                        <p class="text-sm font-medium text-gray-700">Penjumlahan</p>
                    </div>
                    <div class="flex-shrink-0 bg-white rounded-xl shadow-md p-3 text-center category-card">
                        <img src="./assets/images/248.-Dril-Perkalian-1-10.png" alt="Perkalian" class="mx-auto h-15 w-15 mb-1 rounded-full object-cover">
                        <p class="text-sm font-medium text-gray-700">Perkalian</p>
                    </div>
                    <div class="flex-shrink-0 bg-white rounded-xl shadow-md p-3 text-center category-card">
                        <img src="./assets/images/3382c75a40700fd4c30e7aaa72c729b8.jpg" alt="Pembagian" class="mx-auto h-15 w-15 mb-1 rounded-full object-cover">
                        <p class="text-sm font-medium text-gray-700">Pembagian</p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-800 mb-3">Game Pilihan Untukmu</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                           <div class="game-card rounded-xl overflow-hidden shadow-lg">
                            <img src="./assets/images/game-1.jpg" alt="Game Penjumlahan Cepat" class="w-full h-40 object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Penjumlahan Cepat!</h3>
                                <p class="text-sm text-gray-600 mb-3">Latih kecepatan penjumlahanmu sampai puluhan ribu. Cocok untuk kelas 3 & 4.</p>
                                <a href="../Siswa/content/games/addition_speed_challenge.php" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                    Mainkan Sekarang <i class="fas fa-play ml-2"></i>
                                </a>
                            </div>
                        </div>

                    <div class="game-card rounded-xl overflow-hidden shadow-lg">
                        <img src="./assets/images/game-2.png" alt="Game Petualangan Perkalian" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Perkalian Naga</h3>
                            <p class="text-sm text-gray-600 mb-3">Kalahkan naga dengan menjawab soal perkalian. Untuk kelas 4 & 5.</p>
                            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                Mainkan Sekarang <i class="fas fa-play ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="game-card rounded-xl overflow-hidden shadow-lg">
                        <img src="./assets/images/game-3.jpg" alt="Game Pembagian Seru" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Kuis Pembagian Harta</h3>
                            <p class="text-sm text-gray-600 mb-3">Bagikan harta dengan adil, kuasai pembagian!</p>
                            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                Mainkan Sekarang <i class="fas fa-play ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="game-card rounded-xl overflow-hidden shadow-lg">
                        <img src="./assets/images/game-4.jpg" alt="Game Pecahan Pizza" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Pizza Pecahan</h3>
                            <p class="text-sm text-gray-600 mb-3">Belajar pecahan sambil memotong pizza. Asyik dan bikin lapar!</p>
                            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                Mainkan Sekarang <i class="fas fa-play ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="game-card rounded-xl overflow-hidden shadow-lg">
                        <img src="./assets/images/game-5.jpg" alt="Game Geometri" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Bangun Ruang Petualang</h3>
                            <p class="text-sm text-gray-600 mb-3">Jelajahi dunia bangun ruang. Kenali kubus, balok, dan lainnya!</p>
                            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                Mainkan Sekarang <i class="fas fa-play ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="game-card rounded-xl overflow-hidden shadow-lg">
                        <img src="./assets/images/game-6.png" alt="Game Statistik" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">Grafik Seru!</h3>
                            <p class="text-sm text-gray-600 mb-3">Kumpulkan data dan buat grafik. Pelajari rata-rata, median, dan modus!</p>
                            <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                                Mainkan Sekarang <i class="fas fa-play ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-10 md:hidden">
            <div class="flex justify-around items-center h-16">
                <a href="#" class="flex flex-col items-center text-blue-600 font-semibold text-sm">
                    <i class="fas fa-home text-xl mb-1"></i>
                    Beranda
                </a>
                <a href="#" class="flex flex-col items-center text-gray-500 hover:text-blue-600 text-sm">
                    <i class="fas fa-trophy text-xl mb-1"></i>
                    Prestasi
                </a>
                <a href="../siswa/content/profile.php" class="flex flex-col items-center text-gray-500 hover:text-blue-600 text-sm" id="profileLinkBottomNav">
                    <i class="fas fa-user-circle text-xl mb-1"></i>
                    Profil
                </a>
                <a href="../auth/logout.php" class="flex flex-col items-center text-gray-500 hover:text-red-600 text-sm">
                    <i class="fas fa-sign-out-alt text-xl mb-1"></i>
                    Logout
                </a>
            </div>
        </nav>
    </div>

<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    const notyf = new Notyf({
        duration: 3000,
        position: { x: 'right', y: 'top' },
        dismissible: true,
        ripple: true,
        types: [
            { type: 'success', background: '#28a745', icon: { className: 'fas fa-check-circle', tagName: 'i', color: 'white' } },
            { type: 'error', background: '#dc3545', icon: { className: 'fas fa-times-circle', tagName: 'i', color: 'white' } }
        ]
    });

    <?php if (isset($_SESSION['notyf_message'])): ?>
        <?php
        $notyf_type = $_SESSION['notyf_type'] ?? 'success';
        $notyf_message = $_SESSION['notyf_message'];
        ?>
        notyf.<?php echo $notyf_type; ?>('<?php echo addslashes($notyf_message); ?>');
        <?php
        unset($_SESSION['notyf_message']);
        unset($_SESSION['notyf_type']);
        ?>
    <?php endif; ?>

    function showLoader() {
        document.getElementById('loadingOverlay').classList.remove('hidden');
    }

    function hideLoader() {
        const loader = document.getElementById('loadingOverlay');
        if (loader) {
            loader.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        hideLoader();

        const profileLinkHeader = document.getElementById('profileLinkHeader');
        if (profileLinkHeader) {
            profileLinkHeader.addEventListener('click', function(event) {
                event.preventDefault();
                showLoader();
                setTimeout(() => {
                    window.location.href = profileLinkHeader.href;
                }, 500);
            });
        }

        const profileLinkBottomNav = document.getElementById('profileLinkBottomNav');
        if (profileLinkBottomNav) {
            profileLinkBottomNav.addEventListener('click', function(event) {
                event.preventDefault();
                showLoader();
                setTimeout(() => {
                    window.location.href = profileLinkBottomNav.href;
                }, 500);
            });
        }
    });

    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            hideLoader();
        } else {
            hideLoader();
        }
    });

    window.addEventListener('beforeunload', showLoader);

    window.addEventListener('popstate', function(event) {
        hideLoader();
    });
</script>
</body>
</html>