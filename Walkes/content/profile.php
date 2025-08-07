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

        if ($action == 'update_profile') {
            $adminId = $_POST['adminId'];
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password_input = $_POST['password'] ?? '';

            // Validasi input
            if (empty($adminId) || empty($username) || empty($email)) {
                sendJsonResponse('error', 'Semua field (Username, Email) harus diisi.');
            }

            // Validasi email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                sendJsonResponse('error', 'Format email tidak valid.');
            }

            // Prepare update query parts
            $query_parts = ["username = ?", "email = ?"];
            $bind_types = "ss";
            $bind_params = [$username, $email];

            // Add password to update if provided
            if (!empty($password_input)) {
                $password_hashed = password_hash($password_input, PASSWORD_DEFAULT);
                $query_parts[] = "password = ?";
                $bind_types .= "s";
                $bind_params[] = $password_hashed;
            }

            // Get current photo path
            $current_foto_path_db = '';
            $stmt_old_foto = $koneksi->prepare("SELECT foto FROM admin WHERE adminId = ?");
            if ($stmt_old_foto !== false) {
                $stmt_old_foto->bind_param("i", $adminId);
                $stmt_old_foto->execute();
                $stmt_old_foto->bind_result($current_foto_path_db);
                $stmt_old_foto->fetch();
                $stmt_old_foto->close();
            }

            // Handle new photo upload
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $target_dir_physical = '../../upload/profile/';
                
                // Create directory if not exists
                if (!is_dir($target_dir_physical)) {
                    if (!mkdir($target_dir_physical, 0777, true)) {
                        sendJsonResponse('error', 'Gagal membuat folder upload. Pastikan izin tulis!');
                    }
                }

                $imageFileType = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (!in_array($imageFileType, $allowed_types)) {
                    sendJsonResponse('error', 'Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan untuk foto.');
                }

                // Check file size (max 2MB)
                if ($_FILES['foto']['size'] > 2097152) {
                    sendJsonResponse('error', 'Ukuran file terlalu besar. Maksimal 2MB.');
                }

                $new_file_name = uniqid('admin_') . '.' . $imageFileType;
                $target_file_physical = $target_dir_physical . $new_file_name;

                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file_physical)) {
                    $foto_new_db_path = 'upload/profile/' . $new_file_name;
                    $query_parts[] = "foto = ?";
                    $bind_types .= "s";
                    $bind_params[] = $foto_new_db_path;

                    // Delete old photo if exists and not placeholder
                    if (!empty($current_foto_path_db)) {
                        $current_foto_physical_path = '../../' . $current_foto_path_db;
                        if (file_exists($current_foto_physical_path) && strpos($current_foto_path_db, 'placeholder') === false) {
                            unlink($current_foto_physical_path);
                        }
                    }
                } else {
                    sendJsonResponse('error', 'Maaf, terjadi kesalahan saat mengunggah foto baru.');
                }
            }

            // Execute update
            $query_final = "UPDATE admin SET " . implode(", ", $query_parts) . " WHERE adminId = ?";
            $bind_types .= "i";
            $bind_params[] = $adminId;

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
                sendJsonResponse('success', 'Profile berhasil diperbarui!');
            } else {
                if ($koneksi->errno == 1062) {
                    sendJsonResponse('error', 'Gagal memperbarui profile: Email sudah digunakan.');
                } else {
                    sendJsonResponse('error', 'Gagal memperbarui profile: ' . $stmt->error);
                }
            }
            $stmt->close();
        }
    }
    exit();
}

// Get admin profile data
$admin_data = null;
$stmt = $koneksi->prepare("SELECT adminId, username, email, foto, tanggal_dibuat FROM admin WHERE kategori = 'admin' LIMIT 1");
if ($stmt !== false) {
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $admin_data = $result->fetch_assoc();
    }
    $stmt->close();
}

if (!$admin_data) {
    echo "<div class='text-center text-red-600'>Data admin tidak ditemukan.</div>";
    exit();
}
?>

<div x-data="profileManager()" id="profile-page-container" data-current-page="profile">
    
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 rounded-2xl p-8 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10">
                <h1 class="text-4xl font-bold mb-2">Profile Management</h1>
                <p class="text-blue-100 text-lg">Kelola informasi profile admin sistem</p>
            </div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-24 -translate-x-24"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-50 to-purple-50 rounded-full -translate-y-16 translate-x-16"></div>
                
                <div class="text-center relative z-10">
                    <div class="relative inline-block mb-6">
                        <?php 
                        $display_foto_path = '../../assets/images/placeholder-admin.jpg';
                        if (!empty($admin_data['foto'])) {
                            $foto_path = '../../' . $admin_data['foto'];
                            if (file_exists($foto_path)) {
                                $display_foto_path = $foto_path;
                            }
                        }
                        ?>
                        <img src="<?php echo $display_foto_path; ?>" 
                             alt="Admin Photo" 
                             class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg mx-auto"
                             id="profile-photo-display">
                        <div class="absolute -bottom-2 -right-2 bg-green-500 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs"></i>
                        </div>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($admin_data['username']); ?></h2>
                    <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($admin_data['email']); ?></p>
                    <p class="text-sm text-gray-500 mb-6">Administrator</p>
                    
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-4 mb-6">
                        <div class="flex items-center justify-center text-sm text-gray-600">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            <span>Bergabung sejak</span>
                        </div>
                        <p class="text-gray-800 font-semibold mt-1">
                            <?php echo date('d F Y', strtotime($admin_data['tanggal_dibuat'])); ?>
                        </p>
                    </div>
                    
                    <button @click="openEditModal()" 
                            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform transition-all duration-200 hover:scale-105 hover:shadow-xl">
                        <i class="fas fa-edit mr-2"></i> Edit Profile
                    </button>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold text-gray-800">Informasi Profile</h3>
                    <div class="bg-gradient-to-r from-green-100 to-blue-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold">
                        <i class="fas fa-shield-alt mr-1"></i> Verified Admin
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Username Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-6 border border-blue-200">
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-500 p-3 rounded-lg mr-4">
                                <i class="fas fa-user text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600 font-semibold uppercase tracking-wide">Username</p>
                                <p class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($admin_data['username']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Email Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-xl p-6 border border-purple-200">
                        <div class="flex items-center mb-3">
                            <div class="bg-purple-500 p-3 rounded-lg mr-4">
                                <i class="fas fa-envelope text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm text-purple-600 font-semibold uppercase tracking-wide">Email</p>
                                <p class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($admin_data['email']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Admin ID Card -->
                    <div class="bg-gradient-to-br from-green-50 to-teal-100 rounded-xl p-6 border border-green-200">
                        <div class="flex items-center mb-3">
                            <div class="bg-green-500 p-3 rounded-lg mr-4">
                                <i class="fas fa-id-badge text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm text-green-600 font-semibold uppercase tracking-wide">Admin ID</p>
                                <p class="text-xl font-bold text-gray-800">#<?php echo str_pad($admin_data['adminId'], 4, '0', STR_PAD_LEFT); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Role Card -->
                    <div class="bg-gradient-to-br from-orange-50 to-red-100 rounded-xl p-6 border border-orange-200">
                        <div class="flex items-center mb-3">
                            <div class="bg-orange-500 p-3 rounded-lg mr-4">
                                <i class="fas fa-crown text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-sm text-orange-600 font-semibold uppercase tracking-wide">Role</p>
                                <p class="text-xl font-bold text-gray-800">Super Administrator</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="mt-8 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Statistik Aktivitas</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="bg-blue-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-clock"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-800">24/7</p>
                            <p class="text-sm text-gray-600">Online Status</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-green-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-800">100%</p>
                            <p class="text-sm text-gray-600">Access Level</p>
                        </div>
                        <div class="text-center">
                            <div class="bg-purple-500 text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <p class="text-2xl font-bold text-gray-800">Max</p>
                            <p class="text-sm text-gray-600">Security Level</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4"
         style="display: none;">
        
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-2xl mx-auto relative transform transition-all">

            <button type="button" @click="closeModal()" 
                    class="absolute top-6 right-6 text-gray-400 hover:text-gray-700 transition-colors duration-200 focus:outline-none z-10">
                <i class="fas fa-times text-2xl"></i>
            </button>

            <div class="mb-8 text-center">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl p-6 mb-6">
                    <i class="fas fa-user-edit text-4xl mb-3"></i>
                    <h3 class="text-3xl font-extrabold">Edit Profile</h3>
                    <p class="text-blue-100 text-sm mt-2">Perbarui informasi profile admin</p>
                </div>
            </div>

            <form @submit.prevent="submitForm()" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    
                    <!-- Photo Upload Section -->
                    <div class="md:col-span-2 text-center mb-6">
                        <div class="relative inline-block">
                            <img :src="photoPreviewSrc || '<?php echo $display_foto_path; ?>'" 
                                 alt="Profile Preview" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 shadow-lg mx-auto"
                                 id="modal-photo-preview">
                            <label for="foto-upload" 
                                   class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white rounded-full w-10 h-10 flex items-center justify-center cursor-pointer shadow-lg transition-all duration-200 transform hover:scale-110">
                                <i class="fas fa-camera text-sm"></i>
                            </label>
                        </div>
                        <input type="file" 
                               id="foto-upload"
                               @change="handleFileUpload($event)" 
                               accept="image/*" 
                               class="hidden">
                        <p class="text-sm text-gray-500 mt-3">Klik ikon kamera untuk mengubah foto</p>
                        <p class="text-xs text-gray-400">Maksimal 2MB. Format: JPG, PNG, GIF</p>
                    </div>
                    
                    <!-- Username Field -->
                    <div class="space-y-2">
                        <label for="username" class="block text-gray-700 text-sm font-bold">Username</label>
                        <div class="relative">
                            <input type="text" 
                                   x-model="profileData.username" 
                                   class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-gray-800 font-medium" 
                                   placeholder="Masukkan username" 
                                   required>
                            <i class="fas fa-user absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                        </div>
                    </div>
                    
                    <!-- Email Field -->
                    <div class="space-y-2">
                        <label for="email" class="block text-gray-700 text-sm font-bold">Email</label>
                        <div class="relative">
                            <input type="email" 
                                   x-model="profileData.email" 
                                   class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-gray-800 font-medium" 
                                   placeholder="Masukkan email" 
                                   required>
                            <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="md:col-span-2 space-y-2">
                        <label for="password" class="block text-gray-700 text-sm font-bold">Password Baru</label>
                        <div class="relative">
                            <input type="password" 
                                   x-model="profileData.password" 
                                   class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 text-gray-800 font-medium" 
                                   placeholder="Kosongkan jika tidak ingin mengubah password">
                            <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah password</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" 
                            @click="closeModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-8 rounded-xl shadow-md transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-times mr-2"></i>Batal
                    </button>
                    <button type="submit" 
                            :disabled="loading"
                            class="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg flex items-center transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-save mr-2"></i>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function profileManager() {
    return {
        showModal: false,
        loading: false,
        profileData: {
            adminId: '<?php echo $admin_data['adminId']; ?>',
            username: '<?php echo htmlspecialchars($admin_data['username']); ?>',
            email: '<?php echo htmlspecialchars($admin_data['email']); ?>',
            password: ''
        },
        photoPreviewSrc: '',
        selectedFile: null,

        openEditModal() {
            this.showModal = true;
            this.photoPreviewSrc = '';
            this.selectedFile = null;
            this.profileData.password = '';
        },

        closeModal() {
            this.showModal = false;
            setTimeout(() => {
                this.photoPreviewSrc = '';
                this.selectedFile = null;
                this.profileData.password = '';
            }, 300);
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (2MB max)
                if (file.size > 2097152) {
                    this.showNotification('error', 'Ukuran file terlalu besar. Maksimal 2MB.');
                    event.target.value = '';
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    this.showNotification('error', 'Hanya file JPG, JPEG, PNG & GIF yang diizinkan.');
                    event.target.value = '';
                    return;
                }

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
                formData.append('action', 'update_profile');
                formData.append('adminId', this.profileData.adminId);
                formData.append('username', this.profileData.username);
                formData.append('email', this.profileData.email);
                
                if (this.profileData.password) {
                    formData.append('password', this.profileData.password);
                }
                
                if (this.selectedFile) {
                    formData.append('foto', this.selectedFile);
                }

                const response = await fetch('../content/profile.php', {
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
                    setTimeout(() => {
                        this.reloadPage();
                    }, 1000);
                } else {
                    this.showNotification('error', result.message);
                }

            } catch (error) {
                console.error('Error submitting form:', error);
                this.showNotification('error', 'Terjadi kesalahan saat memproses data. Silakan coba lagi.');
            } finally {
                this.loading = false;
            }
        },

        reloadPage() {
            if (typeof window.loadContent === 'function') {
                window.loadContent('profile', '', '');
            } else {
                window.location.reload();
            }
        },

        showNotification(type, message) {
            // Cek apakah Notyf tersedia
            if (typeof Notyf !== 'undefined') {
                const notyf = new Notyf({
                    duration: 4000,
                    position: {
                        x: 'right',
                        y: 'top',
                    },
                    types: [
                        {
                            type: 'success',
                            background: 'linear-gradient(135deg, #10b981, #059669)',
                            icon: {
                                className: 'fas fa-check-circle',
                                tagName: 'i',
                                color: 'white'
                            }
                        },
                        {
                            type: 'error',
                            background: 'linear-gradient(135deg, #ef4444, #dc2626)',
                            icon: {
                                className: 'fas fa-exclamation-circle',
                                tagName: 'i',
                                color: 'white'
                            }
                        }
                    ]
                });
                
                if (type === 'success') {
                    notyf.success(message);
                } else {
                    notyf.error(message);
                }
            } else {
                // Fallback menggunakan alert jika Notyf tidak tersedia
                alert(message);
            }
        }
    }
}
</script>