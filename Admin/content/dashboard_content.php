<?php
require_once '../../config/koneksi.php';

if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

$query_total_siswa = "SELECT COUNT(*) AS total_siswa FROM siswa";
$result_total_siswa = $koneksi->query($query_total_siswa);
$total_siswa = $result_total_siswa->fetch_assoc()['total_siswa'];

$query_total_walikelas = "SELECT COUNT(*) AS total_walikelas FROM walikelas";
$result_total_walikelas = $koneksi->query($query_total_walikelas);
$total_walikelas = $result_total_walikelas->fetch_assoc()['total_walikelas'];

$query_total_games = "SELECT COUNT(*) AS total_games FROM hasil_permainan";
$result_total_games = $koneksi->query($query_total_games);
$total_games = $result_total_games->fetch_assoc()['total_games'];

$query_total_points = "SELECT SUM(skor) AS total_skor FROM hasil_permainan";
$result_total_points = $koneksi->query($query_total_points);
$total_skor_data = $result_total_points->fetch_assoc();
$total_skor = is_null($total_skor_data['total_skor']) ? 0 : $total_skor_data['total_skor'];

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

$jumlah_siswa_leaderboard = count($leaderboard);

$query_avg_skor_siswa = "
    SELECT 
        s.username AS nama_siswa,
        AVG(hp.skor) AS rata_rata_skor
    FROM hasil_permainan hp
    JOIN siswa s ON hp.siswa_id = s.siswaId
    GROUP BY s.siswaId, s.username
    ORDER BY rata_rata_skor DESC
    LIMIT 10
";
$result_avg_skor_siswa = $koneksi->query($query_avg_skor_siswa);
$data_avg_skor = [];
while ($row = $result_avg_skor_siswa->fetch_assoc()) {
    $data_avg_skor[] = $row;
}

$query_games_per_hari = "
    SELECT 
        DATE(tanggal_main) AS tanggal,
        COUNT(*) AS total_games
    FROM hasil_permainan
    GROUP BY tanggal
    ORDER BY tanggal ASC
    LIMIT 30
";
$result_games_per_hari = $koneksi->query($query_games_per_hari);
$data_games_per_hari = [];
while ($row = $result_games_per_hari->fetch_assoc()) {
    $data_games_per_hari[] = $row;
}

$koneksi->close();
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-dark">Gamification Administrator</h1>
    <p class="text-gray-600 mt-2">Monitor student engagement and achievements</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Siswa</h3>
                <p class="text-3xl font-bold text-dark mt-1"><?= htmlspecialchars($total_siswa) ?></p>
                <p class="text-green-500 text-xs mt-2 font-semibold">
                    <i class="fas fa-arrow-up mr-1"></i> Jumlah Siswa Terdaftar
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
                <h3 class="text-gray-500 text-sm font-medium">Total Walikelas</h3>
                <p class="text-3xl font-bold text-dark mt-1"><?= htmlspecialchars($total_walikelas) ?></p>
                <p class="text-green-500 text-xs mt-2 font-semibold">
                    Jumlah Walikelas Terdaftar
                </p>
            </div>
            <div class="bg-teal-500/80 text-white p-4 rounded-lg shadow-md">
                <i class="fas fa-user-tie text-2xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-500 text-sm font-medium">Total Permainan</h3>
                <p class="text-3xl font-bold text-dark mt-1"><?= htmlspecialchars($total_games) ?></p>
                <p class="text-green-500 text-xs mt-2 font-semibold">
                    <i class="fas fa-arrow-up mr-1"></i> Yang Di Selesaikan
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
                    Berdasarkan Total Skor
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
                    <i class="fas fa-arrow-up mr-1"></i> Keseluruhan
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

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-dark">
            <i class="fas fa-chart-bar text-secondary mr-2"></i> Visualisasi Data Permainan
        </h2>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h3 class="text-lg font-semibold text-dark mb-2">Top 10 Siswa Berdasarkan Rata-rata Skor</h3>
            <canvas id="avgSkorChart"></canvas>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h3 class="text-lg font-semibold text-dark mb-2">Jumlah Permainan Selesai per Hari</h3>
            <canvas id="gamesPerHariChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const avgSkorData = <?= json_encode($data_avg_skor) ?>;
    const gamesPerHariData = <?= json_encode($data_games_per_hari) ?>;

    const avgSkorLabels = avgSkorData.map(d => d.nama_siswa);
    const avgSkorValues = avgSkorData.map(d => d.rata_rata_skor);

    const avgSkorCtx = document.getElementById('avgSkorChart').getContext('2d');
    new Chart(avgSkorCtx, {
        type: 'bar',
        data: {
            labels: avgSkorLabels,
            datasets: [{
                label: 'Rata-rata Skor',
                data: avgSkorValues,
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const gamesPerHariLabels = gamesPerHariData.map(d => d.tanggal);
    const gamesPerHariValues = gamesPerHariData.map(d => d.total_games);
    
    const gamesPerHariCtx = document.getElementById('gamesPerHariChart').getContext('2d');
    new Chart(gamesPerHariCtx, {
        type: 'line',
        data: {
            labels: gamesPerHariLabels,
            datasets: [{
                label: 'Jumlah Permainan',
                data: gamesPerHariValues,
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>