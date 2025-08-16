<?php
// Pastikan file koneksi tersedia
require_once '../../config/koneksi.php';

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// -------------------------------------------------------------
// PHP Logic for Dashboard Data
// -------------------------------------------------------------

// Mengambil total siswa
$query_total_siswa = "SELECT COUNT(*) AS total_siswa FROM siswa";
$result_total_siswa = $koneksi->query($query_total_siswa);
$total_siswa = $result_total_siswa->fetch_assoc()['total_siswa'];

// Mengambil total permainan (digunakan sebagai proxy untuk "Badges Awarded")
$query_total_games = "SELECT COUNT(*) AS total_games FROM hasil_permainan";
$result_total_games = $koneksi->query($query_total_games);
$total_games = $result_total_games->fetch_assoc()['total_games'];

// Mengambil total skor keseluruhan
$query_total_points = "SELECT SUM(skor) AS total_skor FROM hasil_permainan";
$result_total_points = $koneksi->query($query_total_points);
$total_skor_data = $result_total_points->fetch_assoc();
$total_skor = is_null($total_skor_data['total_skor']) ? 0 : $total_skor_data['total_skor'];

// Mengambil data leaderboard (tanpa batasan)
$leaderboard = [];
$query_leaderboard = "
    SELECT 
        s.username AS nama_siswa,
        s.kelas AS kelas_siswa,
        SUM(hp.skor) AS total_skor
    FROM hasil_permainan hp
    JOIN siswa s ON hp.siswa_id = s.siswaId
    GROUP BY s.siswaId, s.username, s.kelas
    ORDER BY total_skor DESC
";

$result_leaderboard = $koneksi->query($query_leaderboard);
if ($result_leaderboard) {
    while ($row = $result_leaderboard->fetch_assoc()) {
        $leaderboard[] = $row;
    }
} else {
    error_log("Error Leaderboard Query: " . $koneksi->error);
}

// Menghitung jumlah siswa di leaderboard secara dinamis
$jumlah_siswa_leaderboard = count($leaderboard);

$koneksi->close();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-dark">Gamification Walikelas</h1>
    <p class="text-gray-600 mt-2">Monitor student engagement and achievements</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Siswa</h3>
                <p class="text-3xl font-bold text-dark mt-1"><?= htmlspecialchars($total_siswa) ?></p>
                <p class="text-green-500 text-xs mt-2 font-semibold">
                    <i class="fas fa-arrow-up mr-1"></i> 12% from last month
                </p>
            </div>
            <div class="bg-primary/80 text-white p-4 rounded-lg shadow-md">
                <i class="fas fa-users text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Permainan</h3>
                <p class="text-3xl font-bold text-dark mt-1"><?= htmlspecialchars($total_games) ?></p>
                <p class="text-green-500 text-xs mt-2 font-semibold">
                    <i class="fas fa-arrow-up mr-1"></i> 24% from last month
                </p>
            </div>
            <div class="bg-secondary/80 text-white p-4 rounded-lg shadow-md">
                <i class="fas fa-trophy text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Siswa Peringkat</h3>
                <p class="text-3xl font-bold text-dark mt-1"><?= htmlspecialchars($jumlah_siswa_leaderboard) ?></p>
                <p class="text-green-500 text-xs mt-2 font-semibold">
                    <i class="fas fa-arrow-up mr-1"></i> Berdasarkan Total Skor
                </p>
            </div>
            <div class="bg-accent/80 text-white p-4 rounded-lg shadow-md">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Skor</h3>
                <p class="text-3xl font-bold text-dark mt-1"><?= htmlspecialchars($total_skor) ?></p>
                <p class="text-green-500 text-xs mt-2 font-semibold">
                    <i class="fas fa-arrow-up mr-1"></i> 18% from last month
                </p>
            </div>
            <div class="bg-yellow-500/80 text-white p-4 rounded-lg shadow-md">
                <i class="fas fa-star text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-dark">
            <i class="fas fa-medal text-primary mr-2"></i> Recent Badges Awarded
        </h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
        <div class="text-center group cursor-pointer transform hover:scale-105 transition-transform duration-200">
            <div class="bg-blue-500/80 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 shadow-md group-hover:shadow-lg transition-shadow">
                <i class="fas fa-lightbulb text-2xl"></i>
            </div>
            <p class="text-sm text-gray-700 font-medium group-hover:text-dark">Bright Idea</p>
        </div>
        <div class="text-center group cursor-pointer transform hover:scale-105 transition-transform duration-200">
            <div class="bg-red-500/80 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 shadow-md group-hover:shadow-lg transition-shadow">
                <i class="fas fa-fire text-2xl"></i>
            </div>
            <p class="text-sm text-gray-700 font-medium group-hover:text-dark">Hot Streak</p>
        </div>
        <div class="text-center group cursor-pointer transform hover:scale-105 transition-transform duration-200">
            <div class="bg-green-500/80 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 shadow-md group-hover:shadow-lg transition-shadow">
                <i class="fas fa-check-double text-2xl"></i>
            </div>
            <p class="text-sm text-gray-700 font-medium group-hover:text-dark">Perfect Score</p>
        </div>
        <div class="text-center group cursor-pointer transform hover:scale-105 transition-transform duration-200">
            <div class="bg-yellow-500/80 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 shadow-md group-hover:shadow-lg transition-shadow">
                <i class="fas fa-comments text-2xl"></i>
            </div>
            <p class="text-sm text-gray-700 font-medium group-hover:text-dark">Discussion Star</p>
        </div>
        <div class="text-center group cursor-pointer transform hover:scale-105 transition-transform duration-200">
            <div class="bg-purple-500/80 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 shadow-md group-hover:shadow-lg transition-shadow">
                <i class="fas fa-rocket text-2xl"></i>
            </div>
            <p class="text-sm text-gray-700 font-medium group-hover:text-dark">Fast Learner</p>
        </div>
        <div class="text-center group cursor-pointer transform hover:scale-105 transition-transform duration-200">
            <div class="bg-teal-500/80 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-2 shadow-md group-hover:shadow-lg transition-shadow">
                <i class="fas fa-hand-holding-heart text-2xl"></i>
            </div>
            <p class="text-sm text-gray-700 font-medium group-hover:text-dark">Helper</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-dark">
            <i class="fas fa-chart-line text-primary mr-2"></i> Current Leaderboard
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($leaderboard)): ?>
                    <?php foreach ($leaderboard as $index => $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($index === 0): ?>
                                    <span class="bg-yellow-500 text-white text-sm font-bold px-3 py-1 rounded-full shadow-md">1</span>
                                <?php elseif ($index === 1): ?>
                                    <span class="bg-gray-500 text-white text-sm font-bold px-3 py-1 rounded-full shadow-md">2</span>
                                <?php elseif ($index === 2): ?>
                                    <span class="bg-amber-700 text-white text-sm font-bold px-3 py-1 rounded-full shadow-md">3</span>
                                <?php else: ?>
                                    <span class="bg-primary text-white text-sm font-bold px-3 py-1 rounded-full shadow-md"><?= $index + 1 ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="../assets/images/logo-tutwuri-SD.png" alt="Student" class="w-9 h-9 rounded-full border-2 border-primary mr-3">
                                    <span class="font-medium text-dark"><?= htmlspecialchars($row['nama_siswa']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-normal">
                                <?= htmlspecialchars($row['kelas_siswa']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                    <?= htmlspecialchars($row['total_skor']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Belum ada data permainan untuk ditampilkan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>