<?php
$base_dir = __DIR__ . '/../../..';
require_once $base_dir . '/config/koneksi.php';

// Ambil daftar soal hanya untuk kelas 3 dari database
$soal_list = [];
try {
    $sql = "SELECT soal_id, nama, kategori, kelas, tingkat FROM soal WHERE kelas = 3 ORDER BY soal_id";
    $result = $koneksi->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $soal_list[] = $row;
        }
    }
} catch (Exception $e) {
    // Jika database error, gunakan data fallback yang relevan
    $soal_list = [
        ['soal_id' => 1, 'nama' => 'Soal Penjumlahan', 'kategori' => 'Penjumlahan', 'kelas' => 3, 'tingkat' => 'Mudah'],
        ['soal_id' => 2, 'nama' => 'Soal Perkalian', 'kategori' => 'Perkalian', 'kelas' => 3, 'tingkat' => 'Sedang']
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Materi - Balap Matematika</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="../../src/output.css" rel="stylesheet" onerror="console.log('Local CSS failed')">
    <link rel="icon" type="image/png" href="../../image/logo-tutwuri-SD.png" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .orbitron-font {
            font-family: 'Orbitron', sans-serif;
        }
        .card-hover {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .card-hover:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .difficulty-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .difficulty-mudah { background: #d4edda; color: #155724; }
        .difficulty-sedang { background: #fff3cd; color: #856404; }
        .difficulty-sulit { background: #f8d7da; color: #721c24; }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            color: white;
        }
    </style>
</head>
<body class="p-4">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-white mb-4"></div>
            <p class="text-lg">Memuat game...</p>
        </div>
    </div>

    <div class="container mx-auto max-w-6xl">
        <div class="text-center mb-8">
            <h1 class="text-5xl font-bold orbitron-font text-white mb-4">
                <i class="fas fa-car text-yellow-400"></i>
                BALAP MATEMATIKA
            </h1>
            <p class="text-xl text-gray-200">Pilih materi yang ingin kamu pelajari sambil balapan!</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <?php if (!empty($soal_list)): ?>
                <?php foreach ($soal_list as $soal): ?>
                    <div class="bg-white rounded-xl p-6 text-center card-hover" onclick="startGame(<?= $soal['soal_id'] ?>)">
                        <div class="text-4xl mb-4">
                            <?php
                            // Icon berdasarkan kategori soal
                            switch ($soal['kategori']) {
                                case 'Penjumlahan':
                                case 'Perjumlahan':
                                    echo '<i class="fas fa-plus text-green-500"></i>';
                                    break;
                                case 'Perkalian':
                                    echo '<i class="fas fa-times text-purple-500"></i>';
                                    break;
                                case 'Pengurangan':
                                    echo '<i class="fas fa-minus text-blue-500"></i>';
                                    break;
                                default:
                                    echo '<i class="fas fa-calculator text-gray-500"></i>';
                                    break;
                            }
                            ?>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-2"><?= htmlspecialchars($soal['nama']) ?></h3>
                        <p class="text-gray-600 mb-4 text-sm">Kelas <?= htmlspecialchars($soal['kelas']) ?> - <?= htmlspecialchars($soal['kategori']) ?></p>
                        <div class="difficulty-badge difficulty-<?= strtolower($soal['tingkat']) ?>">
                            Level: <?= htmlspecialchars($soal['tingkat']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center text-white">
                    <p class="text-xl">Belum ada materi soal untuk kelas ini.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-xl p-6">
            <h2 class="text-2xl font-bold text-white mb-4">
                <i class="fas fa-info-circle"></i> Cara Bermain
            </h2>
            <div class="grid md:grid-cols-3 gap-4 text-white">
                <div class="text-center">
                    <div class="text-3xl mb-2">🏁</div>
                    <h3 class="font-bold mb-2">Pilih Materi</h3>
                    <p class="text-sm">Klik salah satu kartu materi di atas untuk memulai balapan</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl mb-2">🚗</div>
                    <h3 class="font-bold mb-2">Jawab Soal</h3>
                    <p class="text-sm">Jawab soal dengan benar untuk mempercepat mobilmu</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl mb-2">🏆</div>
                    <h3 class="font-bold mb-2">Menang!</h3>
                    <p class="text-sm">Kumpulkan poin sebanyak-banyaknya dalam 60 detik</p>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center text-white">
            <p class="text-sm opacity-75">
                <i class="fas fa-database"></i> 
                Terhubung dengan database: <?= !empty($soal_list) ? count($soal_list) . ' materi tersedia' : 'Mode offline' ?>
            </p>
        </div>

        <div class="text-center mt-4 text-white">
            <p class="text-xs opacity-75">© 2024 Game Edukasi Matematika - Belajar Sambil Bermain</p>
        </div>
    </div>

    <script>
        function startGame(soalId) {
            console.log('🎮 Starting game with soal_id:', soalId);
            
            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Disable all cards
            const cards = document.querySelectorAll('.card-hover');
            cards.forEach(card => {
                card.style.pointerEvents = 'none';
                card.style.opacity = '0.7';
            });

            // Small delay for better UX
            setTimeout(() => {
                try {
                    // Navigate to quiz game
                    window.location.href = `../games/quiz_start.php?soal_id=${soalId}`;
                } catch (error) {
                    console.error('❌ Error navigating to game:', error);
                    alert('Gagal memuat game. Silakan coba lagi.');
                    
                    // Re-enable cards
                    cards.forEach(card => {
                        card.style.pointerEvents = 'auto';
                        card.style.opacity = '1';
                    });
                    document.getElementById('loadingOverlay').style.display = 'none';
                }
            }, 500);
        }

        // Add hover effects and interactions
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-hover');
            
            cards.forEach((card, index) => {
                // Add enter animation with delay
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
                
                // Initial state for animation
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                
                // Hover sound effect (optional)
                card.addEventListener('mouseenter', function() {
                    // Add subtle scale effect
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
            
            console.log('✅ Soal selection page loaded with', cards.length, 'materials');
        });

        // Handle back navigation
        window.addEventListener('popstate', function() {
            document.getElementById('loadingOverlay').style.display = 'none';
        });

        // Error handling
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
            document.getElementById('loadingOverlay').style.display = 'none';
        });
    </script>
</body>
</html>