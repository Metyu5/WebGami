<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../../config/koneksi.php';

// Fungsi untuk mengirim response JSON
function sendJsonResponse($status, $message, $data = null) {
    header('Content-Type: application/json');
    $response = ['status' => $status, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'tambah_siswa') {
            $nisn = trim($_POST['nisn']);
            $username = trim($_POST['username']);
            $kelas = trim($_POST['kelas']);
            $password_input = $_POST['password'];

            // Validasi input
            if (empty($nisn) || empty($username) || empty($password_input) || empty($kelas)) {
                sendJsonResponse('error', 'Semua field (NISN, Nama Pengguna, Password, Kelas) harus diisi.');
            }

            // Hash password
            $password_hashed = password_hash($password_input, PASSWORD_DEFAULT);
            $kategori = 'siswa';
            $foto_filename = 'assets/images/placeholder-male.jpg';

            // Handle file upload
            $target_dir_physical = '../../upload/profile/';
            if (!is_dir($target_dir_physical)) {
                if (!mkdir($target_dir_physical, 0777, true)) {
                    sendJsonResponse('error', 'Gagal membuat folder upload. Pastikan izin tulis!');
                }
            }

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $imageFileType = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($imageFileType, $allowed_types)) {
                    sendJsonResponse('error', 'Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan untuk foto.');
                }

                $new_file_name = uniqid('siswa_') . '.' . $imageFileType;
                $target_file_physical = $target_dir_physical . $new_file_name;
                
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file_physical)) {
                    $foto_filename = 'upload/profile/' . $new_file_name;
                } else {
                    sendJsonResponse('error', 'Maaf, terjadi kesalahan saat mengunggah foto.');
                }
            }

            // Insert to database
            $stmt = $koneksi->prepare("INSERT INTO siswa (nisn, username, password, foto, kelas, kategori) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                sendJsonResponse('error', 'Gagal menyiapkan statement INSERT: ' . $koneksi->error);
            }

            $stmt->bind_param("ssssss", $nisn, $username, $password_hashed, $foto_filename, $kelas, $kategori);

            if ($stmt->execute()) {
                sendJsonResponse('success', 'Data siswa berhasil ditambahkan!');
            } else {
                if ($koneksi->errno == 1062) {
                    sendJsonResponse('error', 'Gagal menambahkan data siswa: NISN sudah terdaftar.');
                } else {
                    sendJsonResponse('error', 'Gagal menambahkan data siswa: ' . $stmt->error);
                }
            }
            $stmt->close();
        } 
        
        else if ($action == 'edit_siswa') {
            $siswaId = $_POST['siswaId'];
            $nisn = trim($_POST['nisn']);
            $username = trim($_POST['username']);
            $kelas = trim($_POST['kelas']);
            $password_input = $_POST['password'] ?? '';

            if (empty($siswaId) || empty($nisn) || empty($username) || empty($kelas)) {
                sendJsonResponse('error', 'Semua field (NISN, Nama Pengguna, Kelas) harus diisi untuk update.');
            }

            // Prepare update query parts
            $query_parts = ["nisn = ?", "username = ?", "kelas = ?"]; 
            $bind_types = "sss"; 
            $bind_params = [$nisn, $username, $kelas]; 

            // Add password to update if provided
            if (!empty($password_input)) {
                $password_hashed = password_hash($password_input, PASSWORD_DEFAULT);
                $query_parts[] = "password = ?";
                $bind_types .= "s";
                $bind_params[] = $password_hashed;
            }

            // Get current photo path
            $current_foto_path_db = '';
            $stmt_old_foto = $koneksi->prepare("SELECT foto FROM siswa WHERE siswaId = ?");
            if ($stmt_old_foto !== false) {
                $stmt_old_foto->bind_param("i", $siswaId);
                $stmt_old_foto->execute();
                $stmt_old_foto->bind_result($current_foto_path_db);
                $stmt_old_foto->fetch();
                $stmt_old_foto->close();
            }

            // Handle new photo upload
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $target_dir_physical = '../../upload/profile/';
                $imageFileType = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($imageFileType, $allowed_types)) {
                    sendJsonResponse('error', 'Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan untuk foto.');
                }

                $new_file_name = uniqid('siswa_') . '.' . $imageFileType;
                $target_file_physical = $target_dir_physical . $new_file_name;

                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file_physical)) {
                    $foto_new_db_path = 'upload/profile/' . $new_file_name;
                    $query_parts[] = "foto = ?";
                    $bind_types .= "s";
                    $bind_params[] = $foto_new_db_path;

                    // Delete old photo if not placeholder
                    $current_foto_physical_path = '../../' . $current_foto_path_db;
                    if (!empty($current_foto_path_db) && file_exists($current_foto_physical_path) && strpos($current_foto_path_db, 'placeholder') === false) {
                        unlink($current_foto_physical_path);
                    }
                } else {
                    sendJsonResponse('error', 'Maaf, terjadi kesalahan saat mengunggah foto baru.');
                }
            }

            // Execute update
            $query_final = "UPDATE siswa SET " . implode(", ", $query_parts) . " WHERE siswaId = ?";
            $bind_types .= "i";
            $bind_params[] = $siswaId;

            $stmt = $koneksi->prepare($query_final);
            if ($stmt === false) {
                sendJsonResponse('error', 'Gagal menyiapkan statement UPDATE: ' . $koneksi->error);
            }

            // Bind parameters
            $refs = array();
            foreach($bind_params as $key => $value) {
                $refs[$key] = &$bind_params[$key];
            }
            call_user_func_array(array($stmt, 'bind_param'), array_merge(array($bind_types), $refs));

            if ($stmt->execute()) {
                sendJsonResponse('success', 'Data siswa berhasil diperbarui!');
            } else {
                if ($koneksi->errno == 1062) {
                    sendJsonResponse('error', 'Gagal memperbarui data siswa: NISN sudah terdaftar.');
                } else {
                    sendJsonResponse('error', 'Gagal memperbarui data siswa: ' . $stmt->error);
                }
            }
            $stmt->close();
        } 
        
        else if ($action == 'hapus_siswa') {
            $siswaId = $_POST['siswaId'];

            // Get photo path before deletion
            $foto_to_delete_db = '';
            $stmt_old_foto = $koneksi->prepare("SELECT foto FROM siswa WHERE siswaId = ?");
            if ($stmt_old_foto !== false) {
                $stmt_old_foto->bind_param("i", $siswaId);
                $stmt_old_foto->execute();
                $result_foto = $stmt_old_foto->get_result();
                if ($result_foto->num_rows > 0) {
                    $foto_to_delete_db = $result_foto->fetch_assoc()['foto'];
                }
                $stmt_old_foto->close();
            }

            // Delete from database
            $stmt = $koneksi->prepare("DELETE FROM siswa WHERE siswaId = ?");
            if ($stmt === false) {
                sendJsonResponse('error', 'Gagal menyiapkan statement DELETE: ' . $koneksi->error);
            }

            $stmt->bind_param("i", $siswaId);

            if ($stmt->execute()) {
                // Delete photo file if not placeholder
                $foto_to_delete_physical = '../../' . $foto_to_delete_db;
                if (!empty($foto_to_delete_db) && file_exists($foto_to_delete_physical) && strpos($foto_to_delete_db, 'placeholder') === false) {
                    unlink($foto_to_delete_physical);
                }
                sendJsonResponse('success', 'Data siswa berhasil dihapus!');
            } else {
                sendJsonResponse('error', 'Gagal menghapus data siswa: ' . $stmt->error);
            }
            $stmt->close();
        }
    }
    exit();
}

// Handle GET requests (pagination and search)
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Count total records
$countSql = "SELECT COUNT(*) AS total FROM siswa WHERE kategori = 'siswa'";
if (!empty($searchQuery)) {
    $countSql .= " AND (nisn LIKE ? OR username LIKE ?)";
}

$stmtCount = $koneksi->prepare($countSql);
if ($stmtCount === false) {
    error_log("Prepare count failed: " . $koneksi->error);
    $totalFilteredStudents = 0;
} else {
    if (!empty($searchQuery)) {
        $searchParam = '%' . $searchQuery . '%';
        $stmtCount->bind_param("ss", $searchParam, $searchParam);
    }
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $totalFilteredStudents = $resultCount->fetch_assoc()['total'];
    $stmtCount->close();
}

$totalPages = ($totalFilteredStudents > 0) ? ceil($totalFilteredStudents / $limit) : 1;

if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages;
} elseif ($totalPages == 0) {
    $page = 1;
}

$offset = ($page - 1) * $limit;
if ($offset < 0) $offset = 0;

// Get student records
$sql = "SELECT siswaId, nisn, username, foto, kelas FROM siswa WHERE kategori = 'siswa'";
if (!empty($searchQuery)) {
    $sql .= " AND (nisn LIKE ? OR username LIKE ?)";
}
$sql .= " ORDER BY siswaId DESC LIMIT ?, ?";

$stmt = $koneksi->prepare($sql);
if ($stmt === false) {
    error_log("Prepare select failed: " . $koneksi->error);
    $studentsOnPage = [];
} else {
    if (!empty($searchQuery)) {
        $searchParam = '%' . $searchQuery . '%';
        $stmt->bind_param("ssii", $searchParam, $searchParam, $offset, $limit);
    } else {
        $stmt->bind_param("ii", $offset, $limit);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $studentsOnPage = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$no_start = $offset + 1;
?>

<style>
    /* Add these styles to match matematika.php's look */
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

<div x-data="studentManager()" id="student-page-container" data-current-page="student" x-cloak>

    <div class="gradient-bg text-white py-8 mb-8">
        <div class="container mx-auto px-6">
            <h1 class="text-4xl font-bold text-center mb-2">🎓 Manajemen Data Siswa</h1>
            <p class="text-center text-blue-100">Kelola daftar lengkap siswa, informasi detail mereka.</p>
        </div>
    </div>

    <main class="container mx-auto px-6 pb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 card-hover">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-4 flex-1">
                    <div class="relative w-full">
                        <input type="text" x-model="searchQuery" 
                               @keydown.enter="searchStudents()"
                               placeholder="🔍 Cari siswa berdasarkan NISN atau Nama Pengguna..."
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
                    ✨ Tambah Siswa
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">NISN</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Pengguna</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if (empty($studentsOnPage)) {
                            ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <div class="text-6xl mb-4">📚</div>
                                        <div class="text-xl font-semibold mb-2">Tidak ada data siswa ditemukan.</div>
                                        <div class="text-sm">Coba cari dengan kata kunci lain atau tambah siswa baru.</div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        } else {
                            foreach ($studentsOnPage as $index => $student) {
                                $display_foto_path = '../../' . htmlspecialchars($student['foto']);
                                
                                // Fallback to placeholder if file does not exist or is a placeholder path
                                if (!file_exists($display_foto_path) || empty($student['foto']) || strpos($student['foto'], 'placeholder') !== false) {
                                    $display_foto_path = '../../assets/images/placeholder-male.jpg';
                                }
                                ?>
                                <tr class="hover:bg-blue-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $no_start + $index; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover" src="<?php echo $display_foto_path; ?>" alt="Foto Siswa">
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($student['nisn']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($student['username']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">Kelas <span class="font-bold" ><?php echo htmlspecialchars($student['kelas']); ?></span></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <button type="button" 
                                                @click="openEditModal({
                                                    siswaId: '<?php echo htmlspecialchars($student['siswaId']); ?>',
                                                    nisn: '<?php echo htmlspecialchars($student['nisn']); ?>',
                                                    username: '<?php echo htmlspecialchars($student['username']); ?>',
                                                    kelas: '<?php echo htmlspecialchars($student['kelas']); ?>',
                                                    foto: '<?php echo htmlspecialchars($student['foto']); ?>'
                                                })"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-100 transition-all"
                                                title="Edit">
                                            <i class="fas fa-edit text-lg"></i>
                                        </button>
                                        <button type="button" 
                                                @click="confirmDelete('<?php echo htmlspecialchars($student['siswaId']); ?>')"
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
                        Menampilkan <?php echo $no_start; ?> - <?php echo min($no_start + count($studentsOnPage) - 1, $totalFilteredStudents); ?> dari <?php echo $totalFilteredStudents; ?> hasil
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
                <h3 x-text="isEditMode ? '✏️ Edit Data Siswa' : '✨ Tambah Siswa Baru'" 
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
                        <label for="nisn" class="block text-gray-700 text-sm font-semibold mb-2">NISN:</label>
                        <div class="relative">
                            <input type="text" x-model="currentStudent.nisn" 
                                   class="w-full input-field text-gray-800" 
                                   placeholder="Masukkan NISN" required>
                            <i class="fas fa-id-card absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 px-3 mb-5">
                        <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Nama Pengguna:</label>
                        <div class="relative">
                            <input type="text" x-model="currentStudent.username" 
                                   class="w-full input-field text-gray-800" 
                                   placeholder="Masukkan Nama Pengguna" required>
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 px-3 mb-5">
                        <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password:</label>
                        <div class="relative">
                            <input type="password" x-model="currentStudent.password" 
                                   :required="!isEditMode"
                                   :placeholder="isEditMode ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan Password'"
                                   class="w-full input-field text-gray-800">
                            <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 px-3 mb-5">
                        <label for="kelas" class="block text-gray-700 text-sm font-semibold mb-2">Kelas:</label>
                        <div class="relative">
                            <input type="text" x-model="currentStudent.kelas" 
                                   class="w-full input-field text-gray-800" 
                                   placeholder="Masukkan Kelas" required>
                            <i class="fas fa-school absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <div class="w-full px-3 mb-6">
                        <label for="foto" class="block text-gray-700 text-sm font-semibold mb-2">Foto Profil:</label>
                        <input type="file" @change="handleFileUpload($event)" accept="image/*" 
                               class="w-full text-gray-800 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 transition-all duration-200">
                        
                        <template x-if="photoPreviewSrc">
                            <img :src="photoPreviewSrc" alt="Foto Preview" 
                                 class="mt-4 max-w-xs h-32 w-32 object-cover rounded-full shadow-md mx-auto">
                        </template>
                        
                        <p class="text-sm text-gray-500 mt-2 text-center">Ukuran gambar maksimal 2MB. Format: JPG, PNG, GIF.</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-6 border-t border-gray-200 pt-6">
                    <button type="button" @click="closeModal()" 
                            class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        ❌ Tutup
                    </button>
                    <button type="submit" :disabled="loading"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl flex items-center gap-2 transition-all disabled:opacity-50">
                        <span x-show="!loading">💾 <span x-text="isEditMode ? 'Simpan Perubahan' : 'Tambah Siswa'"></span></span>
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
function studentManager() {
    return {
        showModal: false,
        isEditMode: false,
        loading: false,
        searchQuery: '<?php echo htmlspecialchars($searchQuery); ?>', // Inisialisasi dari PHP
        
        currentStudent: {
            siswaId: '',
            nisn: '',
            username: '',
            password: '',
            kelas: '',
            foto: ''
        },
        photoPreviewSrc: '',
        selectedFile: null,

        openAddModal() {
            this.isEditMode = false;
            this.resetForm();
            this.showModal = true;
            document.body.style.overflow = 'hidden'; // Prevent body scroll
        },

        openEditModal(studentData) {
            this.isEditMode = true;
            this.currentStudent = { ...studentData, password: '' };
            this.photoPreviewSrc = studentData.foto ? '../../' + studentData.foto : '';
            this.showModal = true;
            document.body.style.overflow = 'hidden'; // Prevent body scroll
        },

        closeModal() { 
            this.showModal = false;
            this.resetForm();
            document.body.style.overflow = 'auto'; // Restore body scroll
        },

        resetForm() {
            this.currentStudent = {
                siswaId: '',
                nisn: '',
                username: '',
                password: '',
                kelas: '',
                foto: ''
            };
            this.photoPreviewSrc = '';
            this.selectedFile = null;
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photoPreviewSrc = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.selectedFile = null;
                this.photoPreviewSrc = '';
            }
        },

        async submitForm() {
            if (this.loading) return;
            
            this.loading = true;
            
            try {
                const formData = new FormData();
                formData.append('action', this.isEditMode ? 'edit_siswa' : 'tambah_siswa');
                formData.append('nisn', this.currentStudent.nisn);
                formData.append('username', this.currentStudent.username);
                formData.append('kelas', this.currentStudent.kelas);
                
                if (this.currentStudent.password) {
                    formData.append('password', this.currentStudent.password);
                }
                
                if (this.isEditMode) {
                    formData.append('siswaId', this.currentStudent.siswaId);
                }
                
                if (this.selectedFile) {
                    formData.append('foto', this.selectedFile);
                }

                const response = await fetch('../content/siswa.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                if (result.status === 'success') {
                    this.showNotification('success', result.message);
                    this.closeModal(); // Tutup modal dan reset form
                    

                    if (this.isEditMode) {

                        this.searchQuery = ''; 
                        this.goToPage(1); 
                    } else { 
                        
                        this.searchQuery = ''; 
                        this.goToPage(1); 
                    }
                } else {
                    this.showNotification('error', result.message);
                    this.closeModal(); 
                }

            } catch (error) {
                console.error('Error submitting form:', error);
                this.showNotification('error', 'Terjadi kesalahan saat memproses data. Silakan coba lagi.');
                this.closeModal(); 
            } finally {
                this.loading = false;
            }
        },

        async confirmDelete(siswaId) {
            const result = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data siswa ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33', // Red
                cancelButtonColor: '#6c757d', // Grey
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                await this.deleteStudent(siswaId);
            }
        },

        async deleteStudent(siswaId) {
            try {
                const formData = new FormData();
                formData.append('action', 'hapus_siswa');
                formData.append('siswaId', siswaId);

                const response = await fetch('../content/siswa.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                if (result.status === 'success') {
                    this.showNotification('success', result.message);
                    this.searchQuery = ''; // Kosongkan search query setelah hapus
                    this.goToPage(1); // Muat ulang halaman pertama tanpa filter pencarian
                } else {
                    this.showNotification('error', result.message);
                }

            } catch (error) {
                console.error('Error deleting student:', error);
                this.showNotification('error', 'Terjadi kesalahan saat menghapus data siswa.');
            }
        },

        searchStudents() {
            this.goToPage(1); // Pergi ke halaman pertama saat mencari
        },

        goToPage(pageNumber) {
            const searchParam = this.searchQuery ? `&search=${encodeURIComponent(this.searchQuery)}` : '';
            const newUrl = `?content=siswa&page=${pageNumber}${searchParam}`;
            this.navigateToPage(newUrl);
        },

        navigateToPage(url) {
            if (typeof window.loadContent === 'function') {
                const urlObj = new URL(url, window.location.origin);
                const params = urlObj.searchParams;
                
                const content = params.get('content') || 'student';
                params.delete('content'); 
                
                const paramString = params.toString();
                
                window.loadContent(content, '', paramString);
            } else {
                window.location.href = url;
            }
        },

        reloadPage() {
            if (typeof window.loadContent === 'function') {
                const currentParams = new URLSearchParams(window.location.search);
                const page = currentParams.get('page') || '';
                const search = currentParams.get('search') || '';
                
                const studentParams = new URLSearchParams();
                if (page) studentParams.set('page', page);
                if (search) studentParams.set('search', search); 
                
                window.loadContent('student', '', studentParams.toString());
            } else {
                window.location.reload();
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