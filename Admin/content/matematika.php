<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); 

include_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  
    
    switch ($_POST['action']) {
        case 'get_detail':
            $soal_id = (int)$_POST['soal_id'];
            
            $stmt = $koneksi->prepare("SELECT * FROM soal WHERE soal_id = ?");
            $stmt->bind_param("i", $soal_id);
            $stmt->execute();
            $soal = $stmt->get_result()->fetch_assoc();
            
            $stmt = $koneksi->prepare("SELECT * FROM detail_soal WHERE soal_id = ? ORDER BY detail_id");
            $stmt->bind_param("i", $soal_id);
            $stmt->execute();
            $details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            echo json_encode(['success' => true, 'soal' => $soal, 'details' => $details]);
            exit;
            
        case 'save_soal':
            try {
                $koneksi->begin_transaction();
                
                $soal_id = isset($_POST['soal_id']) ? (int)$_POST['soal_id'] : 0;
                $nama = trim($_POST['nama']);
                $kategori = trim($_POST['kategori']);
                $kelas = (int)$_POST['kelas'];
                $tingkat = $_POST['tingkat'];
                
                if ($soal_id > 0) {
                    $stmt = $koneksi->prepare("UPDATE soal SET nama = ?, kategori = ?, kelas = ?, tingkat = ? WHERE soal_id = ?");
                    $stmt->bind_param("ssisi", $nama, $kategori, $kelas, $tingkat, $soal_id);
                    $stmt->execute();
                } else {
                    $stmt = $koneksi->prepare("INSERT INTO soal (nama, kategori, kelas, tingkat) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssis", $nama, $kategori, $kelas, $tingkat);
                    $stmt->execute();
                    $soal_id = $koneksi->insert_id;
                }
                
                if (isset($_POST['soal_id']) && $_POST['soal_id'] > 0) {
                    $stmt = $koneksi->prepare("DELETE FROM detail_soal WHERE soal_id = ?");
                    $stmt->bind_param("i", $soal_id);
                    $stmt->execute();
                }
                
                if (isset($_POST['pertanyaan']) && is_array($_POST['pertanyaan'])) {
                    $stmt = $koneksi->prepare("INSERT INTO detail_soal (soal_id, pertanyaan, jawaban, skor) VALUES (?, ?, ?, ?)");
                    
                    foreach ($_POST['pertanyaan'] as $index => $pertanyaan) {
                        $jawaban = $_POST['jawaban'][$index] ?? '';
                        $skor = (int)($_POST['skor'][$index] ?? 5);
                        
                        if (!empty(trim($pertanyaan)) && !empty(trim($jawaban))) {
                            $stmt->bind_param("issi", $soal_id, trim($pertanyaan), trim($jawaban), $skor);
                            $stmt->execute();
                        }
                    }
                }
                
                $koneksi->commit();
                echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan!']);
            } catch (Exception $e) {
                $koneksi->rollback();
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
            
        case 'delete_soal':
            try {
                $koneksi->begin_transaction();
                
                $soal_id = (int)$_POST['soal_id'];
                
                $stmt = $koneksi->prepare("DELETE FROM detail_soal WHERE soal_id = ?");
                $stmt->bind_param("i", $soal_id);
                $stmt->execute();
                
                $stmt = $koneksi->prepare("DELETE FROM soal WHERE soal_id = ?");
                $stmt->bind_param("i", $soal_id);
                $stmt->execute();
                
               $koneksi->commit();
                echo json_encode([
                    'success' => true, 
                    'message' => 'Data berhasil dihapus!'
                ], JSON_UNESCAPED_UNICODE);
                
            } catch (Exception $e) {
                $koneksi->rollback();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error: ' . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
            }
            exit;
            
        default:
            echo json_encode([
                'success' => false, 
                'message' => 'Action tidak valid'
            ], JSON_UNESCAPED_UNICODE);
            exit;
    }
}


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $kelas_filter = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $where_conditions = [];
    $params = [];
    $types = '';

    if (!empty($search)) {
        $where_conditions[] = "(nama LIKE ? OR kategori LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ss';
    }

    if ($kelas_filter > 0) {
        $where_conditions[] = "kelas = ?";
        $params[] = $kelas_filter;
        $types .= 'i';
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    $count_query = "SELECT COUNT(*) as total FROM soal $where_clause";
    if (!empty($params)) {
        $stmt = $koneksi->prepare($count_query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $total_records = $stmt->get_result()->fetch_assoc()['total'];
    } else {
        $total_records = $koneksi->query($count_query)->fetch_assoc()['total'];
    }

    $total_pages = ceil($total_records / $limit);

    $query = "SELECT * FROM soal $where_clause ORDER BY soal_id DESC LIMIT $limit OFFSET $offset";
    if (!empty($params)) {
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $koneksi->query($query);
    }

    $soal_list = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'soal_list' => $soal_list,
        'total_records' => $total_records,
        'total_pages' => $total_pages,
        'current_page' => $page,
        'offset' => $offset,
        'limit' => $limit,
        'search' => $search,
        'kelas_filter' => $kelas_filter
    ]);
    exit; 
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$kelas_filter = isset($_GET['kelas']) ? (int)$_GET['kelas'] : 0;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
?>

<style>
    [x-cloak] { display: none !important; }
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

<div x-data="soalManager()" x-cloak>
    <div class="gradient-bg text-white py-8 mb-8">
        <div class="container mx-auto px-6">
            <h1 class="text-4xl font-bold text-center mb-2">Manajemen Soal Matematika</h1>
            <p class="text-center text-blue-100">Kelola soal matematika dengan mudah dan efisien</p>
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
                               placeholder="Cari soal..."
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
                                    Semua Kelas
                                </button>
                               <template x-for="i in Array.from({length: 3}, (_, k) => k + 3)" :key="i">
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
                
                <button @click="openModal('add')" 
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl flex items-center gap-2 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah Soal
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-normal text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-normal text-gray-600 uppercase tracking-wider">Nama Soal</th>
                            <th class="px-6 py-4 text-left text-xs font-normal text-gray-600 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-normal text-gray-600 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-normal text-gray-600 uppercase tracking-wider">Tingkat</th>
                            <th class="px-6 py-4 text-right text-xs font-normal text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-if="soalListData.length > 0">
                            <template x-for="(soal, index) in soalListData" :key="soal.soal_id">
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="offset + index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-normal text-gray-900" x-text="soal.nama"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs font-medium rounded-full bg-blue-100 text-blue-800" x-text="soal.kategori"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        Kelas <span x-text="soal.kelas"></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs font-normal rounded-full"
                                              :class="{
                                                  'bg-green-100 text-green-800': soal.tingkat === 'Mudah',
                                                  'bg-yellow-100 text-yellow-800': soal.tingkat === 'Sedang',
                                                  'bg-red-100 text-red-800': soal.tingkat === 'Sulit',
                                                  'bg-gray-100 text-gray-800': !['Mudah', 'Sedang', 'Sulit'].includes(soal.tingkat)
                                              }"
                                              x-text="soal.tingkat">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="viewDetail(soal.soal_id)" 
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-100 transition-all" 
                                                title="Lihat Detail">
                                            👁️
                                        </button>
                                        <button @click="editSoal(soal.soal_id)" 
                                                class="text-yellow-600 hover:text-yellow-900 p-2 rounded-full hover:bg-yellow-100 transition-all" 
                                                title="Edit">
                                            ✏️
                                        </button>
                                        <button @click="confirmDelete(soal.soal_id)" 
                                                class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-100 transition-all" 
                                                title="Hapus">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </template>
                        
                        <template x-if="soalListData.length === 0">
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="text-6xl mb-4">📚</div>
                                        <div class="text-xl font-semibold mb-2">Tidak ada data soal</div>
                                        <div class="text-sm">Silakan tambah soal baru untuk memulai</div>
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

    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         style="display: none;">
        
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-90"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
            
            <div class="sticky top-0 bg-white rounded-t-2xl border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-800" x-text="modalTitle"></h3>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <form @submit.prevent="saveSoal()">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-normal text-gray-700 mb-2">Nama Soal</label>
                            <input type="text" 
                                   x-model="form.nama" 
                                   :disabled="mode === 'view'"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-50 transition-all"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-normal text-gray-700 mb-2">Kategori</label>
                            <input type="text" 
                                   x-model="form.kategori" 
                                   :disabled="mode === 'view'"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-50 transition-all"
                                   required>
                        </div>

                        <div x-data="{ openKelasModal: false, selectedKelasModal: form.kelas }" 
                             @click.away="openKelasModal = false" class="relative"
                             x-init="$watch('form.kelas', value => selectedKelasModal = value)">
                            <label class="block text-sm font-normal text-gray-700 mb-2">Kelas</label>
                            <button type="button" 
                                    @click="openKelasModal = !openKelasModal"
                                    :disabled="mode === 'view'"
                                    class="flex items-center justify-between w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all disabled:bg-gray-50 cursor-pointer bg-white text-gray-700">
                                <span x-text="'Kelas ' + selectedKelasModal"></span>
                                <svg class="ml-2 h-4 w-4 transform transition-transform duration-200"
                                     :class="{ 'rotate-180': openKelasModal }"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="openKelasModal"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 transform"
                                 x-transition:enter-end="opacity-100 scale-100 transform"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100 transform"
                                 x-transition:leave-end="opacity-0 scale-95 transform"
                                 class="absolute z-20 mt-2 w-full rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                                 style="display: none;">
                                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="kelas-modal-menu">
                                    <template x-for="i in 6" :key="i">
                                        <button type="button" 
                                                @click="selectedKelasModal = i; form.kelas = i; openKelasModal = false" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-colors" 
                                                role="menuitem"
                                                :class="{ 'bg-blue-100 text-blue-900 font-semibold': selectedKelasModal === i }">
                                            Kelas <span x-text="i"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div x-data="{ openTingkatModal: false, selectedTingkatModal: form.tingkat, tingkatOptions: ['Mudah', 'Sedang', 'Sulit'] }" 
                             @click.away="openTingkatModal = false" class="relative"
                             x-init="$watch('form.tingkat', value => selectedTingkatModal = value)">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">⭐ Tingkat</label>
                            <button type="button" 
                                    @click="openTingkatModal = !openTingkatModal"
                                    :disabled="mode === 'view'"
                                    class="flex items-center justify-between w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all disabled:bg-gray-50 cursor-pointer bg-white text-gray-700">
                                <span x-text="selectedTingkatModal"></span>
                                <svg class="ml-2 h-4 w-4 transform transition-transform duration-200"
                                     :class="{ 'rotate-180': openTingkatModal }"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="openTingkatModal"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 transform"
                                 x-transition:enter-end="opacity-100 scale-100 transform"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100 transform"
                                 x-transition:leave-end="opacity-0 scale-95 transform"
                                 class="absolute z-20 mt-2 w-full rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden"
                                 style="display: none;">
                                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="tingkat-modal-menu">
                                    <template x-for="option in tingkatOptions" :key="option">
                                        <button type="button" 
                                                @click="selectedTingkatModal = option; form.tingkat = option; openTingkatModal = false" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-900 transition-colors" 
                                                role="menuitem"
                                                :class="{ 
                                                    'bg-blue-100 text-blue-900 font-semibold': selectedTingkatModal === option,
                                                    'text-green-800': option === 'Mudah' && selectedTingkatModal !== option,
                                                    'text-yellow-800': option === 'Sedang' && selectedTingkatModal !== option,
                                                    'text-red-800': option === 'Sulit' && selectedTingkatModal !== option
                                                }">
                                            <span x-text="option === 'Mudah' ? '🟢 Mudah' : (option === 'Sedang' ? '🟡 Sedang' : '🔴 Sulit')"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold text-gray-800">❓ Daftar Pertanyaan</h4>
                            <button type="button" 
                                    x-show="mode !== 'view'"
                                    @click="addQuestion()"
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl text-sm flex items-center gap-2 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                Tambah Pertanyaan
                            </button>
                        </div>
                        
                        <div class="space-y-4" x-show="form.questions.length > 0">
                            <template x-for="(question, index) in form.questions" :key="index">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="text-sm font-semibold text-gray-600">Pertanyaan </span>
                                        <button type="button" 
                                                x-show="mode !== 'view'"
                                                @click="removeQuestion(index)"
                                                class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 transition-colors">
                                            🗑️
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                        <div class="md:col-span-6">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Pertanyaan</label>
                                            <textarea x-model="question.pertanyaan" 
                                                     :disabled="mode === 'view'"
                                                     class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 text-sm transition-all"
                                                     rows="2" required></textarea>
                                        </div>
                                        <div class="md:col-span-4">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Jawaban</label>
                                            <input type="text" 
                                                   x-model="question.jawaban" 
                                                   :disabled="mode === 'view'"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 text-sm transition-all"
                                                   required>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Skor</label>
                                            <input type="number" 
                                                   x-model="question.skor" 
                                                   :disabled="mode === 'view'"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 text-sm transition-all"
                                                   min="1" max="100" required>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        
                        <div x-show="form.questions.length === 0" class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">❓</div>
                            <div>Belum ada pertanyaan. Klik "Tambah Pertanyaan" untuk memulai.</div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" 
                                @click="closeModal()"
                                class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                            ❌ Tutup
                        </button>
                        <button type="submit" 
                                x-show="mode !== 'view'"
                                :disabled="loading"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl flex items-center gap-2 transition-all disabled:opacity-50">
                            <span x-show="!loading">💾 Simpan</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function soalManager() {
        return {
            showModal: false,
            mode: 'add', 
            loading: false,
            search: '<?= htmlspecialchars($search) ?>',
            kelasFilter: <?= $kelas_filter ?>,
            
            soalListData: [],
            totalRecords: 0,
            totalPages: 0,
            currentPage: <?= $page ?>,
            limit: 10, 
            offset: 0, 

            form: {
                soal_id: null,
                nama: '',
                kategori: '',
                kelas: 1,
                tingkat: 'Mudah',
                questions: []
            },

            get modalTitle() {
                switch(this.mode) {
                    case 'add': return '✨ Tambah Soal Baru';
                    case 'edit': return '✏️ Edit Soal';
                    case 'view': return '👁️ Detail Soal';
                    default: return 'Soal';
                }
            },

            init() {
                this.loadData(this.currentPage); 
                console.log('Soal Manager initialized');
            },

            async loadData(page = 1) {
                this.loading = true; 
                this.currentPage = page; 
                this.offset = (this.currentPage - 1) * this.limit; 

                const params = new URLSearchParams();
                if (this.search) params.append('search', this.search);
                if (this.kelasFilter > 0) params.append('kelas', this.kelasFilter);
                params.append('page', this.currentPage);
                
                try {
                    const response = await fetch(`../content/matematika.php?${params.toString()}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', 
                            'Content-Type': 'application/json' 
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (data && data.success) {
                        this.soalListData = data.soal_list;
                        this.totalRecords = data.total_records;
                        this.totalPages = data.total_pages;
                        this.currentPage = data.current_page;
                        this.offset = data.offset;
                        this.limit = data.limit;
                    } else {
                        this.showAlert('Error', 'Gagal memuat data: ' + (data?.message || 'Server response error'), 'error');
                    }
                } catch (error) {
                    console.error('Error loading soal data:', error);
                    this.showAlert('Error', 'Terjadi kesalahan saat memuat data: ' + error.message, 'error');
                } finally {
                    this.loading = false;
                }
            },

            // Modal functions
            openModal(mode, soalId = null) {
                this.mode = mode;
                this.showModal = true;
                this.resetForm();
                
                if ((mode === 'edit' || mode === 'view') && soalId) {
                    this.loadSoalDetail(soalId);
                }
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.showModal = false;
                this.resetForm();
                document.body.style.overflow = 'auto';
                this.loadData(this.currentPage); // Reload data to reflect changes if any
            },

            resetForm() {
                this.form = {
                    soal_id: null,
                    nama: '',
                    kategori: '',
                    kelas: 1,
                    tingkat: 'Mudah',
                    questions: []
                };
                this.loading = false;
            },

            // Question management
            addQuestion() {
                this.form.questions.push({
                    pertanyaan: '',
                    jawaban: '',
                    skor: 5
                });
            },

            removeQuestion(index) {
                // Modified to use SweetAlert2 for confirmation before removing a question
                Swal.fire({
                    title: 'Hapus Pertanyaan?',
                    text: "Pertanyaan ini akan dihapus secara permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.form.questions.splice(index, 1);
                        this.showAlert('Berhasil!', 'Pertanyaan berhasil dihapus.', 'success');
                    }
                });
            },

            // AJAX functions
            async loadSoalDetail(soalId) {
                try {
                    this.loading = true;
                    
                    const formData = new FormData();
                    formData.append('action', 'get_detail');
                    formData.append('soal_id', soalId);
                    
                    const response = await fetch('../content/matematika.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const text = await response.text();
                    console.log('Raw response:', text); // Debug log
                    
                    let jsonText = text.trim();
                    const jsonStart = jsonText.indexOf('{"');
                    if (jsonStart > 0) {
                        jsonText = jsonText.substring(jsonStart);
                    }
                    const jsonEnd = jsonText.lastIndexOf('}');
                    if (jsonEnd !== -1 && jsonEnd < jsonText.length - 1) {
                        jsonText = jsonText.substring(0, jsonEnd + 1);
                    }
                    jsonText = jsonText.replace(/[\r\n\t]/g, '').replace(/\s+/g, ' ');
                    
                    console.log('Cleaned JSON:', jsonText); // Debug log
                    
                    let data;
                    try {
                        data = JSON.parse(jsonText);
                    } catch (parseError) {
                        console.error('JSON Parse Error:', parseError);
                        console.error('Failed to parse:', jsonText);
                        throw new Error('Invalid JSON response from server');
                    }
                    
                    if (data && data.success) {
                        const soal = data.soal;
                        this.form = {
                            soal_id: soal.soal_id,
                            nama: soal.nama,
                            kategori: soal.kategori,
                            kelas: parseInt(soal.kelas),
                            tingkat: soal.tingkat,
                            questions: (data.details || []).map(detail => ({
                                pertanyaan: detail.pertanyaan,
                                jawaban: detail.jawaban,
                                skor: parseInt(detail.skor)
                            }))
                        };
                    } else {
                        this.showAlert('Error', 'Gagal memuat data: ' + (data?.message || 'Server response error'), 'error');
                    }
                } catch (error) {
                    console.error('Error loading soal detail:', error);
                    this.showAlert('Error', 'Terjadi kesalahan saat memuat data: ' + error.message, 'error');
                } finally {
                    this.loading = false;
                }
            },

            async saveSoal() {
                if (this.form.questions.length === 0) {
                    this.showAlert('Peringatan!', 'Minimal harus ada 1 pertanyaan', 'warning');
                    return;
                }

                try {
                    this.loading = true;
                    
                    const formData = new FormData();
                    formData.append('action', 'save_soal');
                    
                    if (this.form.soal_id) {
                        formData.append('soal_id', this.form.soal_id);
                    }
                    
                    formData.append('nama', this.form.nama);
                    formData.append('kategori', this.form.kategori);
                    formData.append('kelas', this.form.kelas);
                    formData.append('tingkat', this.form.tingkat);
                    
                    // Add questions
                    this.form.questions.forEach((question, index) => {
                        formData.append(`pertanyaan[${index}]`, question.pertanyaan);
                        formData.append(`jawaban[${index}]`, question.jawaban);
                        formData.append(`skor[${index}]`, question.skor);
                    });
                    
                    const response = await fetch('../content/matematika.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const text = await response.text();
                    console.log('Save response:', text); // Debug log
                    
                    let jsonText = text.trim();
                    const jsonStart = jsonText.indexOf('{"');
                    if (jsonStart > 0) {
                        jsonText = jsonText.substring(jsonStart);
                    }
                    const jsonEnd = jsonText.lastIndexOf('}');
                    if (jsonEnd !== -1 && jsonEnd < jsonText.length - 1) {
                        jsonText = jsonText.substring(0, jsonEnd + 1);
                    }
                    jsonText = jsonText.replace(/[\r\n\t]/g, '').replace(/\s+/g, ' ');
                    
                    let data;
                    try {
                        data = JSON.parse(jsonText);
                    } catch (parseError) {
                        console.error('JSON Parse Error:', parseError);
                        console.error('Failed to parse:', jsonText);
                        throw new Error('Invalid JSON response from server');
                    }
                    
                    if (data && data.success) {
                        this.showAlert('Berhasil!', data.message, 'success');
                        this.closeModal(); // This will trigger loadData()
                    } else {
                        this.showAlert('Error!', (data?.message || 'Server response error'), 'error');
                    }
                } catch (error) {
                    console.error('Error saving soal:', error);
                    this.showAlert('Error!', 'Terjadi kesalahan saat menyimpan data: ' + error.message, 'error');
                } finally {
                    this.loading = false;
                }
            },

            // View functions
            viewDetail(soalId) {
                this.openModal('view');
                this.loadSoalDetail(soalId);
            },

            editSoal(soalId) {
                this.openModal('edit');
                this.loadSoalDetail(soalId);
            },

            // Delete function
            confirmDelete(soalId) {
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Anda tidak akan bisa mengembalikan ini! Semua pertanyaan yang terkait juga akan dihapus.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.deleteSoal(soalId);
                    }
                });
            },

            async deleteSoal(soalId) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'delete_soal');
                    formData.append('soal_id', soalId);
                    
                    const response = await fetch('../content/matematika.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const text = await response.text();
                    console.log('Delete response:', text); // Debug log
                    
                    let jsonText = text.trim();
                    const jsonStart = jsonText.indexOf('{"');
                    if (jsonStart > 0) {
                        jsonText = jsonText.substring(jsonStart);
                    }
                    const jsonEnd = jsonText.lastIndexOf('}');
                    if (jsonEnd !== -1 && jsonEnd < jsonText.length - 1) {
                        jsonText = jsonText.substring(0, jsonEnd + 1);
                    }
                    jsonText = jsonText.replace(/[\r\n\t]/g, '').replace(/\s+/g, ' ');
                    
                    let data;
                    try {
                        data = JSON.parse(jsonText);
                    } catch (parseError) {
                        console.error('JSON Parse Error:', parseError);
                        console.error('Failed to parse:', jsonText);
                        throw new Error('Invalid JSON response from server');
                    }
                    
                    if (data && data.success) {
                        this.showAlert('Dihapus!', data.message, 'success');
                        this.loadData(this.currentPage); // Reload data after delete
                    } else {
                        this.showAlert('Error!', (data?.message || 'Server response error'), 'error');
                    }
                } catch (error) {
                    console.error('Error deleting soal:', error);
                    this.showAlert('Error!', 'Terjadi kesalahan saat menghapus data: ' + error.message, 'error');
                }
            },

            // Alert function (using SweetAlert2)
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
            },

            // Keyboard shortcuts
            handleKeydown(event) {
                if (event.key === 'Escape' && this.showModal) {
                    this.closeModal();
                }
                
                if (event.ctrlKey && event.key === 's' && this.showModal && this.mode !== 'view') {
                    event.preventDefault();
                    this.saveSoal();
                }
            }
        }
    }

    document.addEventListener('keydown', function(event) {
        const soalManagerInstance = window.Alpine?.getDataProxy?.(document.querySelector('[x-data="soalManager()"]'));
        if (soalManagerInstance && typeof soalManagerInstance.handleKeydown === 'function') {
            soalManagerInstance.handleKeydown(event);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA' && event.target.form) {
            event.preventDefault();
        }
    });
</script>