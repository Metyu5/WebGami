<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../config/koneksi.php'; 

// Fungsi untuk mengirim response JSON
function sendJsonResponse($status, $message, $data = null) {
    header('Content-Type: application/json');
    $response = ['status' => $status, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit(); // Sangat penting: Hentikan eksekusi setelah mengirim JSON
}

// Handle AJAX requests for CRUD operations
// Mengubah kondisi deteksi AJAX: Cukup cek $_POST['action']
if (isset($_POST['action'])) {
    
    $action = $_POST['action'];

    if ($action === 'tambah') {
        $nip = trim($_POST['nip']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password']; 

        // Validasi input
        if (empty($nip) || empty($username) || empty($email) || empty($password)) {
            sendJsonResponse('error', 'Semua field (NIP, Nama Pengguna, Email, Password) harus diisi.');
        }

        // Hash password sebelum menyimpan ke database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $kategori = 'wali kelas'; 

        $stmt = mysqli_prepare($koneksi, "INSERT INTO walikelas (nip, username, email, password, kategori) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $nip, $username, $email, $hashedPassword, $kategori);
            if (mysqli_stmt_execute($stmt)) {
                sendJsonResponse('success', 'Data Wali Kelas berhasil ditambahkan!');
            } else {
                if (mysqli_errno($koneksi) == 1062) { // Duplicate entry error
                    sendJsonResponse('error', 'NIP atau Email sudah terdaftar.');
                } else {
                    sendJsonResponse('error', 'Gagal menambahkan data Wali Kelas: ' . mysqli_error($koneksi));
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            sendJsonResponse('error', 'Gagal menyiapkan statement: ' . mysqli_error($koneksi));
        }
    } 
    
    else if ($action === 'edit') {
        // Menggunakan walkesId sesuai struktur tabel
        $walkesId = $_POST['id']; // Tetap gunakan $_POST['id'] dari frontend, tapi mapping ke walkesId di PHP
        $nip = trim($_POST['nip']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password']; // Password bisa kosong jika tidak diubah

        if (empty($walkesId) || empty($nip) || empty($username) || empty($email)) {
            sendJsonResponse('error', 'Semua field (NIP, Nama Pengguna, Email) harus diisi untuk update.');
        }

        $query_parts = ["nip = ?", "username = ?", "email = ?"];
        $bind_types = "sss";
        $bind_params = [&$nip, &$username, &$email]; // Menggunakan referensi untuk bind_param

        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query_parts[] = "password = ?";
            $bind_types .= "s";
            $bind_params[] = &$hashedPassword;
        }

        // Mengubah ID menjadi walkesId di WHERE clause
        $query = "UPDATE walikelas SET " . implode(", ", $query_parts) . " WHERE walkesId = ?";
        $bind_types .= "i";
        $bind_params[] = &$walkesId; // Tambahkan walkesId sebagai parameter terakhir

        $stmt = mysqli_prepare($koneksi, $query);
        if ($stmt) {
            // Gunakan call_user_func_array untuk bind_param dengan parameter referensi
            call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt, $bind_types], $bind_params));

            if (mysqli_stmt_execute($stmt)) {
                sendJsonResponse('success', 'Data Wali Kelas berhasil diperbarui!');
            } else {
                if (mysqli_errno($koneksi) == 1062) { // Duplicate entry error
                    sendJsonResponse('error', 'NIP atau Email sudah terdaftar.');
                } else {
                    sendJsonResponse('error', 'Gagal memperbarui data Wali Kelas: ' . mysqli_error($koneksi));
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            sendJsonResponse('error', 'Gagal menyiapkan statement: ' . mysqli_error($koneksi));
        }
    }
    
    else if ($action === 'hapus') {
        // Menggunakan walkesId sesuai struktur tabel
        $walkesId = $_POST['id']; // Tetap gunakan $_POST['id'] dari frontend
        if (empty($walkesId)) {
            sendJsonResponse('error', 'ID tidak valid untuk penghapusan.');
        }

        // Mengubah ID menjadi walkesId di WHERE clause
        $stmt = mysqli_prepare($koneksi, "DELETE FROM walikelas WHERE walkesId = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $walkesId); // Bind walkesId
            if (mysqli_stmt_execute($stmt)) {
                sendJsonResponse('success', 'Data Wali Kelas berhasil dihapus!');
            } else {
                sendJsonResponse('error', 'Gagal menghapus data Wali Kelas: ' . mysqli_error($koneksi));
            }
            mysqli_stmt_close($stmt);
        } else {
            sendJsonResponse('error', 'Gagal menyiapkan statement: ' . mysqli_error($koneksi));
        }
    }
    // Tidak perlu exit() di sini karena sendJsonResponse sudah memanggil exit()
}

// Handle GET requests (pagination and search) - Ini akan dieksekusi hanya jika BUKAN request AJAX POST
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Count total records
$countSql = "SELECT COUNT(*) AS total FROM walikelas";
if (!empty($searchQuery)) {
    $countSql .= " WHERE nip LIKE ? OR username LIKE ? OR email LIKE ?";
}

$stmtCount = mysqli_prepare($koneksi, $countSql);
if ($stmtCount === false) {
    error_log("Prepare count failed: " . mysqli_error($koneksi));
    $totalFilteredWalikelas = 0;
} else {
    if (!empty($searchQuery)) {
        $searchParam = '%' . $searchQuery . '%';
        mysqli_stmt_bind_param($stmtCount, "sss", $searchParam, $searchParam, $searchParam);
    }
    mysqli_stmt_execute($stmtCount);
    $resultCount = mysqli_stmt_get_result($stmtCount);
    $totalFilteredWalikelas = mysqli_fetch_assoc($resultCount)['total'];
    mysqli_stmt_close($stmtCount);
}

$totalPages = ($totalFilteredWalikelas > 0) ? ceil($totalFilteredWalikelas / $limit) : 1;

if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages;
} elseif ($totalPages == 0) {
    $page = 1;
}

$offset = ($page - 1) * $limit;
if ($offset < 0) $offset = 0;

// Get walikelas records
$sql = "SELECT walkesId, nip, username, email FROM walikelas";
if (!empty($searchQuery)) {
    $sql .= " WHERE nip LIKE ? OR username LIKE ? OR email LIKE ?";
}
$sql .= " ORDER BY walkesId DESC LIMIT ?, ?";

$stmt = mysqli_prepare($koneksi, $sql);
if ($stmt === false) {
    error_log("Prepare select failed: " . mysqli_error($koneksi));
    $walikelasOnPage = [];
} else {
    if (!empty($searchQuery)) {
        $searchParam = '%' . $searchQuery . '%';
        mysqli_stmt_bind_param($stmt, "sssi", $searchParam, $searchParam, $searchParam, $offset, $limit);
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $offset, $limit);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $walikelasOnPage = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
}

$no_start = $offset + 1;
?>

<style>
    /* Global styles for consistency */
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
    /* Specific styles for modal to enhance appearance */
    .modal-content {
        background-color: #ffffff;
        border-radius: 1.5rem; /* rounded-2xl */
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.08); /* shadow-2xl */
    }
    .modal-header {
        border-bottom: 1px solid #e5e7eb; /* border-gray-200 */
        padding: 1.5rem; /* p-6 */
    }
    .input-field {
        transition: all 0.2s;
        border-radius: 0.5rem; /* rounded-lg */
        padding-left: 2.5rem; /* pl-10 */
        border: 1px solid #d1d5db; /* border-gray-300 */
    }
    .input-field:focus {
        outline: none;
        ring: 2px;
        ring-color: #6366f1; /* focus:ring-primary */
        border-color: transparent;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div x-data="waliKelasManager()" id="wali_kelas-page-container" data-current-page="wali_kelas" x-cloak>

    <div class="gradient-bg text-white py-8 mb-8">
        <div class="container mx-auto px-6">
            <h1 class="text-4xl font-bold text-center mb-2"> 👨🏻‍🎓 Manajemen Data Wali Kelas</h1>
            <p class="text-center text-blue-100">Kelola daftar lengkap Wali Kelas, informasi detail mereka.</p>
        </div>
    </div>

    <main class="container mx-auto px-6 pb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 card-hover">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-4 flex-1">
                    <div class="relative w-full">
                        <input type="text" x-model="searchQuery" 
                               @keydown.enter="searchWaliKelas()"
                               placeholder="🔍 Cari Wali Kelas berdasarkan NIP, Nama Pengguna, atau Email..."
                               class="pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full sm:w-80 transition-all duration-300 ease-in-out shadow-sm hover:shadow-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search h-5 w-5 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <button @click="openAddModal()"
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl flex items-center gap-2 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    ✨ Tambah Wali Kelas
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">NIP</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Pengguna</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if (empty($walikelasOnPage)) {
                            ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="text-6xl mb-4">📚</div>
                                        <div class="text-xl font-semibold mb-2">Tidak ada data Wali Kelas ditemukan.</div>
                                        <div class="text-sm">Coba cari dengan kata kunci lain atau tambah Wali Kelas baru.</div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        } else {
                            foreach ($walikelasOnPage as $index => $wk) {
                                ?>
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $no_start + $index; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($wk['nip']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($wk['username']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium"><?php echo htmlspecialchars($wk['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <button type="button" 
                                                @click="openEditModal({
                                                    walkesId: '<?php echo htmlspecialchars($wk['walkesId']); ?>',
                                                    nip: '<?php echo htmlspecialchars($wk['nip']); ?>',
                                                    username: '<?php echo htmlspecialchars($wk['username']); ?>',
                                                    email: '<?php echo htmlspecialchars($wk['email']); ?>'
                                                })"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-100 transition-all"
                                                title="Edit">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        <button type="button" 
                                                @click="confirmDelete('<?php echo htmlspecialchars($wk['walkesId']); ?>')"
                                                class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-100 transition-all"
                                                title="Hapus">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan <?php echo $no_start; ?> - <?php echo min($no_start + count($walikelasOnPage) - 1, $totalFilteredWalikelas); ?> dari <?php echo $totalFilteredWalikelas; ?> hasil
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($page > 1): ?>
                            <button @click="goToPage(<?php echo $page - 1; ?>)" 
                                    class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                                ← Previous
                            </button>
                        <?php else: ?>
                            <button class="px-3 py-2 rounded-lg border border-gray-300 bg-gray-200 text-sm text-gray-400 cursor-not-allowed">
                                ← Previous
                            </button>
                        <?php endif; ?>

                        <?php 
                        // Pagination numbers
                        $start_page = max(1, $page - 2);
                        $end_page = min($totalPages, $page + 2);

                        if ($start_page > 1) {
                            echo '<button @click="goToPage(1)" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50 transition-colors">1</button>';
                            if ($start_page > 2) {
                                echo '<span class="px-3 py-2 text-gray-500 text-sm">...</span>';
                            }
                        }

                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <button @click="goToPage(<?php echo $i; ?>)" 
                                    class="px-3 py-2 rounded-lg border text-sm transition-colors
                                    <?php echo ($i == $page) ? 'bg-blue-500 text-white border-blue-500' : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>

                        <?php if ($end_page < $totalPages): ?>
                            <?php if ($end_page < $totalPages - 1): ?>
                                <span class="px-3 py-2 text-gray-500 text-sm">...</span>
                            <?php endif; ?>
                            <button @click="goToPage(<?php echo $totalPages; ?>)" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50 transition-colors"><?php echo $totalPages; ?></button>
                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                            <button @click="goToPage(<?php echo $page + 1; ?>)" 
                                    class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50 transition-colors">
                                Next →
                            </button>
                        <?php else: ?>
                            <button class="px-3 py-2 rounded-lg border border-gray-300 bg-gray-200 text-sm text-gray-400 cursor-not-allowed">
                                Next →
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         style="display: none;">
        
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 transform scale-90" 
             x-transition:enter-end="opacity-100 transform scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 transform scale-100" 
             x-transition:leave-end="opacity-0 transform scale-90"
             class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-xl mx-auto relative transform transition-all modal-content">

            <div class="sticky top-0 bg-white rounded-t-2xl border-b border-gray-200 px-6 py-4 flex justify-between items-center -mx-8 -mt-8 mb-6 modal-header">
                <h3 x-text="isEditMode ? '✏️ Edit Data Wali Kelas' : '✨ Tambah Wali Kelas Baru'" 
                    class="text-2xl font-bold text-gray-800"></h3>
                <button type="button" @click="closeModal()" 
                        class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form @submit.prevent="submitForm()" enctype="multipart/form-data">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-5">
                        <label for="nip" class="block text-gray-700 text-sm font-semibold mb-2">NIP:</label>
                        <div class="relative">
                            <input type="text" x-model="currentWalikelas.nip" 
                                   class="w-full input-field text-gray-800" 
                                   placeholder="Masukkan NIP" required>
                            <i class="fas fa-id-badge absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 px-3 mb-5">
                        <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Nama Pengguna:</label>
                        <div class="relative">
                            <input type="text" x-model="currentWalikelas.username" 
                                   class="w-full input-field text-gray-800" 
                                   placeholder="Masukkan Nama Pengguna" required>
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 px-3 mb-5">
                        <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email:</label>
                        <div class="relative">
                            <input type="email" x-model="currentWalikelas.email" 
                                   class="w-full input-field text-gray-800" 
                                   placeholder="Masukkan Email" required>
                            <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 px-3 mb-5">
                        <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password:</label>
                        <div class="relative">
                            <input type="password" x-model="currentWalikelas.password" 
                                   :required="!isEditMode"
                                   :placeholder="isEditMode ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan Password'"
                                   class="w-full input-field text-gray-800">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-6 border-t border-gray-200 pt-6">
                    <button type="button" @click="closeModal()" 
                            class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        ❌ Tutup
                    </button>
                    <button type="submit" :disabled="loading"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl flex items-center gap-2 transition-all disabled:opacity-50">
                        <span x-show="!loading">💾 <span x-text="isEditMode ? 'Simpan Perubahan' : 'Tambah Wali Kelas'"></span></span>
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

<script>
function waliKelasManager() {
    return {
        showModal: false,
        isEditMode: false,
        loading: false,
        searchQuery: '<?php echo htmlspecialchars($searchQuery); ?>',
        
        currentWalikelas: {
            walkesId: '',
            nip: '',
            username: '',
            email: '',
            password: ''
        },

        openAddModal() {
            this.isEditMode = false;
            this.resetForm();
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },

        openEditModal(walikelasData) {
            this.isEditMode = true;
            this.currentWalikelas = { ...walikelasData, password: '' }; 
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() { 
            this.showModal = false;
            this.resetForm();
            document.body.style.overflow = 'auto';
        },

        resetForm() {
            this.currentWalikelas = {
                walkesId: '',
                nip: '',
                username: '',
                email: '',
                password: ''
            };
        },

        async submitForm() {
            if (this.loading) return;
            
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('action', this.isEditMode ? 'edit' : 'tambah');
                formData.append('nip', this.currentWalikelas.nip);
                formData.append('username', this.currentWalikelas.username);
                formData.append('email', this.currentWalikelas.email);
                
                if (this.currentWalikelas.password) {
                    formData.append('password', this.currentWalikelas.password);
                }
                
                if (this.isEditMode) {
                    formData.append('id', this.currentWalikelas.walkesId); 
                }

                const response = await fetch('../content/wali_kelas.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                if (result.status === 'success') {
                    this.showNotification('success', result.message);
                    this.closeModal();
                    this.searchQuery = '';
                    this.goToPage(1);
                } else {
                    this.showNotification('error', result.message);
                    // Tidak menutup modal jika ada error validasi dari server
                }

            }
             catch (error) {
                console.error('Error submitting form:', error);
                this.showNotification('error', 'Terjadi kesalahan saat memproses data. Silakan coba lagi.');
            } finally {
                this.loading = false;
            }
        },

        async confirmDelete(walkesId) {
            const result = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data Wali Kelas ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                await this.deleteWalikelas(walkesId);
            }
        },

        async deleteWalikelas(walkesId) {
            try {
                const formData = new FormData();
                formData.append('action', 'hapus');
                formData.append('id', walkesId);

                const response = await fetch('../content/wali_kelas.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                if (result.status === 'success') {
                    this.showNotification('success', result.message);
                    this.searchQuery = '';
                    this.goToPage(1);
                } else {
                    this.showNotification('error', result.message);
                }

            } catch (error) {
                console.error('Error deleting Wali Kelas:', error);
                this.showNotification('error', 'Terjadi kesalahan saat menghapus data Wali Kelas.');
            }
        },

        searchWaliKelas() {
            this.goToPage(1);
        },

        goToPage(pageNumber) {
            const searchParam = this.searchQuery ? `&search=${encodeURIComponent(this.searchQuery)}` : '';
            const newUrl = `?content=wali_kelas&page=${pageNumber}${searchParam}`;
            this.navigateToPage(newUrl);
        },

        navigateToPage(url) {
            if (typeof window.loadContent === 'function') {
                const urlObj = new URL(url, window.location.origin);
                const params = urlObj.searchParams;
                
                const content = params.get('content') || 'wali_kelas';
                params.delete('content'); 
                
                const paramString = params.toString();
                
                window.loadContent(content, '', paramString);
            } else {
                window.location.href = url;
            }
        },

        showNotification(type, message) {
            let iconType = 'info';
            if (type === 'success') {
                iconType = 'success';
            } else if (type === 'error') {
                iconType = 'error';
            } else if (type === 'warning') {
                iconType = 'warning';
            }

            Swal.fire({
                title: message,
                icon: iconType,
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