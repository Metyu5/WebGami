<?php
session_start();

if (!isset($_SESSION['siswaId'])) {
    header("Location: ../../index2.php?error=Anda harus login untuk mengakses halaman ini.");
    exit();
}

require_once '../../config/koneksi.php';

// Ambil ID siswa dari sesi yang sedang login
$siswa_id = $_SESSION['siswaId'];

// Query untuk mengambil data riwayat permainan dari tabel hasil_permainan
$sql = "SELECT * FROM hasil_permainan WHERE siswa_id = ? ORDER BY tanggal_main DESC";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $siswa_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
}

$stmt->close();
$koneksi->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Permainan</title>
    <link href="../../src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        .scroll-hidden::-webkit-scrollbar { display: none; }
        .scroll-hidden { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white text-gray-800 p-4 shadow-md flex items-center justify-center relative">
            <h1 class="text-xl font-bold tracking-tight">Riwayat Permainan</h1>
        </header>

        <main class="flex-1 overflow-y-auto p-4 pb-20">
            <?php if (empty($history)): ?>
                <div class="text-center text-gray-500 mt-16">
                    <i class="fas fa-history text-6xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-semibold">Belum ada riwayat permainan.</p>
                    <p class="text-sm text-gray-400 mt-2">Ayo mainkan game sekarang dan lihat riwayatmu di sini!</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($history as $game): ?>
                        <div class="bg-white rounded-xl shadow-lg p-5 border-l-4 border-blue-500 transform transition duration-300 hover:scale-105 hover:shadow-2xl">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium px-2 py-1 rounded-full <?php echo strtolower($game['tingkat_kesulitan']) == 'easy' ? 'bg-green-100 text-green-700' : (strtolower($game['tingkat_kesulitan']) == 'medium' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'); ?>">
                                    <?php echo htmlspecialchars(ucfirst($game['tingkat_kesulitan'])); ?>
                                </span>
                                <span class="text-sm font-bold text-blue-600">Skor: <?php echo htmlspecialchars($game['skor']); ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span class="text-sm">Benar: <?php echo htmlspecialchars($game['jawaban_benar']); ?> dari <?php echo htmlspecialchars($game['total_pertanyaan']); ?></span>
                            </div>
                            <div class="flex items-center text-gray-400 mt-2">
                                <i class="fas fa-calendar-alt text-sm mr-2"></i>
                                <span class="text-xs"><?php echo date('d M Y, H:i', strtotime($game['tanggal_main'])); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-10 md:hidden">
            <div class="flex justify-around items-center h-16">
                <a href="/WebGami/Siswa/dashboard.php" class="flex flex-col items-center text-gray-500 hover:text-blue-600 text-sm">
                    <i class="fas fa-home text-xl mb-1"></i>Beranda
                </a>
                <a href="/WebGami/Siswa/history.php" class="flex flex-col items-center text-blue-600 font-semibold text-sm">
                    <i class="fas fa-trophy text-xl mb-1"></i>History
                </a>
                <a href="/WebGami/Siswa/content/profile.php" class="flex flex-col items-center text-gray-500 hover:text-blue-600 text-sm">
                    <i class="fas fa-user-circle text-xl mb-1"></i>Profil
                </a>
                <a href="/WebGami/auth/logout.php" class="flex flex-col items-center text-gray-500 hover:text-red-600 text-sm">
                    <i class="fas fa-sign-out-alt text-xl mb-1"></i>Logout
                </a>
            </div>
        </nav>
    </div>
</body>
</html>