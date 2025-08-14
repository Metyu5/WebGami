<?php


if (!file_exists('../../config/koneksi.php')) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'File koneksi.php tidak ditemukan.']);
    exit;
}
include_once '../../config/koneksi.php';

if ($koneksi->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $koneksi->connect_error]);
    exit;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');

    try {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $kelas_filter = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $where_conditions = [];
        $params = [];
        $types = '';

        $query = "
            SELECT
                hp.id,
                hp.skor,
                hp.jawaban_benar,
                hp.total_pertanyaan,
                hp.tingkat_kesulitan,
                hp.tanggal_main,
                s.username AS nama_siswa,
                s.kelas AS kelas_siswa,
                sl.nama AS nama_soal
            FROM hasil_permainan hp
            JOIN siswa s ON hp.siswa_id = s.siswaId  -- KOREKSI: Menggunakan `s.siswaId`
            JOIN soal sl ON hp.soal_id = sl.soal_id
        ";

        if (!empty($search)) {
            $where_conditions[] = "(s.username LIKE ? OR sl.nama LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'ss';
        }

        if ($kelas_filter > 0) {
            $where_conditions[] = "s.kelas = ?";
            $params[] = $kelas_filter;
            $types .= 'i';
        }

        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

        $count_query = "
            SELECT COUNT(*) as total
            FROM hasil_permainan hp
            JOIN siswa s ON hp.siswa_id = s.siswaId  -- KOREKSI: Menggunakan `s.siswaId`
            JOIN soal sl ON hp.soal_id = sl.soal_id
            $where_clause
        ";

        $stmt_count = $koneksi->prepare($count_query);
        if (!$stmt_count) {
            throw new Exception("Prepare count failed: " . $koneksi->error);
        }
        if (!empty($params)) {
            $stmt_count->bind_param($types, ...$params);
        }
        $stmt_count->execute();
        $total_records = $stmt_count->get_result()->fetch_assoc()['total'];
        $stmt_count->close();

        $total_pages = ceil($total_records / $limit);

        $query .= " $where_clause ORDER BY hp.tanggal_main DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $koneksi->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare query failed: " . $koneksi->error);
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $history_list = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        echo json_encode([
            'success' => true,
            'history_list' => $history_list,
            'total_records' => $total_records,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'offset' => $offset,
            'limit' => $limit,
            'search' => $search,
            'kelas_filter' => $kelas_filter
        ]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()]);
        exit;
    }
}

// HTML Section: Renders the full page content
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kelas_filter = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
?>

<style>
    [x-cloak] {
        display: none !important;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div x-data="historyManager()" x-cloak>
    <div class="gradient-bg text-white py-8 mb-8">
        <div class="container mx-auto px-6">
            <h1 class="text-4xl font-bold text-center mb-2">📚 Manajemen Riwayat Permainan </h1>
            <p class="text-center text-blue-100">Lihat dan kelola riwayat permainan siswa</p>
        </div>
    </div>

    <main class="container mx-auto px-6 pb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 card-hover">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-4 flex-1">
                    <div class="relative">
                        <input type="text"
                            x-model="search"
                            @input.debounce.500ms="loadData()"
                            placeholder="🔍 Cari nama siswa/soal..."
                            class="pl-4 pr-10 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-64 transition-all duration-300 ease-in-out shadow-sm hover:shadow-md">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div x-data="{ open: false, selectedKelas: kelasFilter }" @click.away="open = false" class="relative">
                        <button type="button"
                            @click="open = !open"
                            class="flex items-center justify-between w-full sm:w-48 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 ease-in-out shadow-sm hover:shadow-md cursor-pointer bg-white text-gray-700">
                            <span x-text="selectedKelas === 0 ? '🎓 Semua Kelas' : 'Kelas ' + selectedKelas"></span>
                            <svg class="ml-2 h-4 w-4 transform transition-transform duration-200"
                                :class="{ 'rotate-180': open }"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 transform"
                            x-transition:enter-end="opacity-100 scale-100 transform"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 transform"
                            x-transition:leave-end="opacity-0 scale-95 transform"
                            class="absolute z-10 mt-2 w-full sm:w-48 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                            style="display: none;">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <button type="button"
                                    @click="selectedKelas = 0; kelasFilter = 0; open = false; loadData()"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-colors"
                                    role="menuitem"
                                    :class="{ 'bg-blue-100 text-blue-900 font-semibold': selectedKelas === 0 }">
                                    🎓 Semua Kelas
                                </button>
                                <template x-for="i in [3, 4, 5]" :key="i">
                                    <button type="button"
                                        @click="selectedKelas = i; kelasFilter = i; open = false; loadData()"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-colors"
                                        role="menuitem"
                                        :class="{ 'bg-blue-100 text-blue-900 font-semibold': selectedKelas === i }">
                                        Kelas <span x-text="i"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">👤 Nama Siswa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">📝 Nama Soal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">💯 Skor</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">✅ Jawaban Benar</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">❓ Total Pertanyaan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">⭐ Tingkat Kesulitan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">📅 Tanggal Main</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="historyListData.length > 0">
                            <template x-for="(history, index) in historyListData" :key="history.id">
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="offset + index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900" x-text="history.nama_siswa"></div>
                                        <div class="text-xs text-gray-500">Kelas <span x-text="history.kelas_siswa"></span></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs font-medium rounded-full bg-blue-100 text-blue-800" x-text="history.nama_soal"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-bold" x-text="history.skor"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="history.jawaban_benar"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="history.total_pertanyaan"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full"
                                            :class="{
                                                  'bg-green-100 text-green-800': history.tingkat_kesulitan === 'Mudah',
                                                  'bg-yellow-100 text-yellow-800': history.tingkat_kesulitan === 'Sedang',
                                                  'bg-red-100 text-red-800': history.tingkat_kesulitan === 'Sulit',
                                              }"
                                            x-text="history.tingkat_kesulitan">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600" x-text="new Date(history.tanggal_main).toLocaleString()"></td>
                                </tr>
                            </template>
                        </template>

                        <template x-if="historyListData.length === 0">
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="text-6xl mb-4">📜</div>
                                        <div class="text-xl font-semibold mb-2">Tidak ada riwayat permainan</div>
                                        <div class="text-sm">Belum ada siswa yang menyelesaikan permainan</div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <template x-if="totalPages > 1">
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Menampilkan <span x-text="offset + 1"></span> - <span x-text="Math.min(offset + limit, totalRecords)"></span> dari <span x-text="totalRecords"></span> hasil
                        </div>
                        <div class="flex space-x-1">
                            <button x-show="currentPage > 1" @click="loadData(currentPage - 1)"
                                class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                                ← Prev
                            </button>

                            <template x-for="i in Array.from({ length: totalPages }, (_, k) => k + 1)" :key="i">
                                <button x-show="i >= Math.max(1, currentPage - 2) && i <= Math.min(totalPages, currentPage + 2)"
                                    @click="loadData(i)"
                                    class="px-3 py-2 rounded-lg border text-sm transition-colors"
                                    :class="i == currentPage ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'">
                                    <span x-text="i"></span>
                                </button>
                            </template>

                            <button x-show="currentPage < totalPages" @click="loadData(currentPage + 1)"
                                class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                                Next →
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </main>
</div>

<script>
    function historyManager() {
        return {
            loading: false,
            search: '<?= htmlspecialchars($search) ?>',
            kelasFilter: <?= $kelas_filter ?>,

            historyListData: [],
            totalRecords: 0,
            totalPages: 0,
            currentPage: <?= $page ?>,
            limit: 10,
            offset: 0,

            init() {
                this.loadData(this.currentPage);
                console.log('History Manager initialized');
            },

            async loadData(page = 1) {
                this.loading = true;
                this.currentPage = page;
                this.offset = (this.currentPage - 1) * this.limit;

                const params = new URLSearchParams();
                if (this.search) params.append('search', this.search);
                if (this.kelasFilter > 0) params.append('kelas', this.kelasFilter);
                params.append('page', this.currentPage);

                const fetchUrl = `../content/history.php?${params.toString()}`;

                console.log('DEBUG: Attempting to fetch URL:', fetchUrl);

                try {
                    const response = await fetch(fetchUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        try {
                            const errorData = JSON.parse(errorText);
                            throw new Error(errorData.message || 'Server returned an error');
                        } catch (e) {
                            throw new Error(`HTTP error! status: ${response.status}. Server response: ${errorText}`);
                        }
                    }

                    const data = await response.json();

                    if (data && data.success) {
                        this.historyListData = data.history_list;
                        this.totalRecords = data.total_records;
                        this.totalPages = data.total_pages;
                        this.currentPage = data.current_page;
                        this.offset = data.offset;
                        this.limit = data.limit;
                    } else {
                        this.showAlert('Error', 'Gagal memuat data: ' + (data?.message || 'Server response error'), 'error');
                    }
                } catch (error) {
                    console.error('Error loading history data:', error);
                    this.showAlert('Error', 'Terjadi kesalahan saat memuat data: ' + error.message, 'error');
                } finally {
                    this.loading = false;
                }
            },

            showAlert(title, message, icon) {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: icon,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            }
        }
    }
</script>