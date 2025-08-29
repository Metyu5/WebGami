<?php
session_start();

if (!isset($_SESSION['siswaId'])) {
    header("Location: ../../index2.php?error=Anda harus login untuk mengakses halaman profil.");
    exit();
}

require_once '../../config/koneksi.php';

$siswa_id_from_session = $_SESSION['siswaId'];

$username_siswa = 'Pengguna';
$nisn_siswa = 'Belum Tersedia';
$kelas_siswa = 'Belum Ditentukan';
$foto_siswa = '../../image/default_avatar.png'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'edit_profile') {
            $new_username = trim($_POST['username']);
            $new_nisn = trim($_POST['nisn']);
            $new_kelas = trim($_POST['kelas']);

            if (empty($new_username) || empty($new_nisn) || empty($new_kelas)) {
                $_SESSION['notyf_message'] = "Semua field profil harus diisi.";
                $_SESSION['notyf_type'] = "error";
            } else {
                $update_foto = '';
                $foto_path_for_db = ''; 
                $force_refresh_session_foto = false;

                $upload_dir = '../../upload/profile/'; 
            

                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                $max_file_size = 2 * 1024 * 1024;

                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        $_SESSION['notyf_message'] = "Gagal membuat direktori upload: " . $upload_dir;
                        $_SESSION['notyf_type'] = "error";
                        header("Location: profile.php");
                        exit();
                    }
                }

                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                    $file_name = $_FILES['foto']['name'];
                    $file_tmp = $_FILES['foto']['tmp_name'];
                    $file_size = $_FILES['foto']['size'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    if (!in_array($file_ext, $allowed_types)) {
                        $_SESSION['notyf_message'] = "Format foto tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.";
                        $_SESSION['notyf_type'] = "error";
                    } elseif ($file_size > $max_file_size) {
                        $_SESSION['notyf_message'] = "Ukuran foto terlalu besar (maks 2MB).";
                        $_SESSION['notyf_type'] = "error";
                    } else {
                        // Hapus foto lama jika ada dan bukan default avatar
                        $stmt_old_foto = $koneksi->prepare("SELECT foto FROM siswa WHERE siswaId = ?");
                        $stmt_old_foto->bind_param("i", $siswa_id_from_session);
                        $stmt_old_foto->execute();
                        $result_old_foto = $stmt_old_foto->get_result();
                        if ($row_old_foto = $result_old_foto->fetch_assoc()) {
                            $old_foto_db_path = $row_old_foto['foto'];

                           
                            $full_old_foto_path_to_unlink = '../../' . $old_foto_db_path;


                            if (!empty($old_foto_db_path) && file_exists($full_old_foto_path_to_unlink) &&
                                strpos($old_foto_db_path, 'image/default_avatar.png') === false) {
                                @unlink($full_old_foto_path_to_unlink);
                            }
                        }
                        $stmt_old_foto->close();

                        $new_file_name = uniqid('profile_') . '.' . $file_ext;
                        $destination = $upload_dir . $new_file_name;

                        if (move_uploaded_file($file_tmp, $destination)) {
                            $update_foto = ", foto = ?";
                            $foto_path_for_db = 'upload/profile/' . $new_file_name; // Path yang akan disimpan di DB dan Sesi
                            $force_refresh_session_foto = true;
                        } else {
                            $_SESSION['notyf_message'] = "Gagal mengupload foto. Periksa izin direktori dan ukuran file.";
                            $_SESSION['notyf_type'] = "error";
                        }
                    }
                }

                if (!isset($_SESSION['notyf_type']) || $_SESSION['notyf_type'] != 'error') {
                    $sql = "UPDATE siswa SET username = ?, nisn = ?, kelas = ?" . $update_foto . " WHERE siswaId = ?";
                    $stmt = $koneksi->prepare($sql);

                    if ($update_foto) {
                        $stmt->bind_param("ssssi", $new_username, $new_nisn, $new_kelas, $foto_path_for_db, $siswa_id_from_session);
                    } else {
                        $stmt->bind_param("sssi", $new_username, $new_nisn, $new_kelas, $siswa_id_from_session);
                    }

                    if ($stmt->execute()) {
                        $_SESSION['username'] = $new_username;
                        if ($force_refresh_session_foto) {
                            $_SESSION['foto'] = $foto_path_for_db; 
                        }
                        $_SESSION['notyf_message'] = "Profil berhasil diperbarui!";
                        $_SESSION['notyf_type'] = "success";
                    } else {
                        $_SESSION['notyf_message'] = "Gagal memperbarui profil: " . $stmt->error;
                        $_SESSION['notyf_type'] = "error";
                    }
                    $stmt->close();
                }
            }
            header("Location: profile.php");
            exit();

        }
        else if ($_POST['action'] === 'change_password') {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            $stmt_pass = $koneksi->prepare("SELECT password FROM siswa WHERE siswaId = ?");
            $stmt_pass->bind_param("i", $siswa_id_from_session);
            $stmt_pass->execute();
            $result_pass = $stmt_pass->get_result();
            if ($row_pass = $result_pass->fetch_assoc()) {
                $hashed_password_from_db = $row_pass['password'];

                if (!password_verify($current_password, $hashed_password_from_db)) {
                    $_SESSION['notyf_message'] = "Password saat ini salah.";
                    $_SESSION['notyf_type'] = "error";
                } elseif ($new_password !== $confirm_password) {
                    $_SESSION['notyf_message'] = "Password baru dan konfirmasi password tidak cocok.";
                    $_SESSION['notyf_type'] = "error";
                } elseif (strlen($new_password) < 6) {
                    $_SESSION['notyf_message'] = "Password baru minimal 6 karakter.";
                    $_SESSION['notyf_type'] = "error";
                } else {
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $stmt_update_pass = $koneksi->prepare("UPDATE siswa SET password = ? WHERE siswaId = ?");
                    $stmt_update_pass->bind_param("si", $new_hashed_password, $siswa_id_from_session);
                    if ($stmt_update_pass->execute()) {
                        $_SESSION['notyf_message'] = "Password berhasil diubah!";
                        $_SESSION['notyf_type'] = "success";
                    } else {
                        $_SESSION['notyf_message'] = "Gagal mengubah password: " . $stmt_update_pass->error;
                        $_SESSION['notyf_type'] = "error";
                    }
                    $stmt_update_pass->close();
                }
            } else {
                $_SESSION['notyf_message'] = "Terjadi kesalahan saat mengambil data pengguna.";
                $_SESSION['notyf_type'] = "error";
            }
            $stmt_pass->close();
            header("Location: profile.php");
            exit();
        }
    }
}

try {
    $stmt = $koneksi->prepare("SELECT username, nisn, foto, kelas FROM siswa WHERE siswaId = ?");
    $stmt->bind_param("i", $siswa_id_from_session);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username_siswa = htmlspecialchars($row['username']);
        $nisn_siswa = htmlspecialchars($row['nisn']);
        $kelas_siswa = htmlspecialchars($row['kelas']);

        if (!empty($row['foto'])) {
            $foto_siswa_db_path = htmlspecialchars($row['foto']); 
            $foto_siswa = '../../' . $foto_siswa_db_path . '?' . time(); 
        } else {
            $foto_siswa = '../../image/default_avatar.png';
        }

    } else {
        error_log("Data siswaId " . $siswa_id_from_session . " tidak ditemukan di database.");
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log("Database error in profile.php: " . $e->getMessage());
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - MathQuest</title>
    <link href="../../src/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
        }
    </style>
</head>
<body class="bg-blue-50">
    <div id="loadingOverlay" class="fixed inset-0 bg-white bg-opacity-20 flex flex-col items-center justify-center z-[9999] hidden">
        <div>
            <img src="../../image/logo-192.png" alt="Memuat..." class="h-16 w-16 mb-4 animate-bounce">
        </div>

        <div class="flex flex-col items-center mt-8">
            <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-red-500 mb-2"></div>
            <p class="text-red-700 text-sm font-semibold">Memuat...</p>
        </div>
    </div>
    <div x-data="{ showEditProfileModal: false, showChangePasswordModal: false }" class="min-h-screen flex flex-col">
        <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 shadow-lg flex items-center justify-between">
            <button id="backButton" class="text-white text-xl p-2 rounded-full hover:bg-white hover:bg-opacity-20 transition duration-200">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="text-2xl font-bold tracking-tight text-center flex-1">Profil Saya</h1>
            <div class="w-10 h-10"></div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8 lg:p-12">
            <div class="max-w-md mx-auto bg-white rounded-xl shadow-lg overflow-hidden md:max-w-2xl">
                <div class="md:flex">
                    <div class="md:flex-shrink-0 flex justify-center items-center p-4">
                        <img class="h-40 w-40 object-cover rounded-full border-4 border-blue-400 shadow-md" src="<?php echo $foto_siswa; ?>" alt="Foto Profil">
                    </div>
                    <div class="p-8 w-full text-center md:text-left">
                        <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold mb-1">Informasi Akun</div>
                        <h2 class="block mt-1 text-2xl leading-tight font-extrabold text-gray-900"><?php echo $username_siswa; ?></h2>
                        <p class="mt-4 text-gray-600 text-sm">NISN: <span class="font-medium text-gray-800"><?php echo $nisn_siswa; ?></span></p>
                        <p class="mt-1 text-gray-600 text-sm">Kelas: <span class="font-medium text-gray-800"><?php echo $kelas_siswa; ?></span></p>

                        <div class="mt-6 flex flex-col space-y-3">
                            <button @click="showEditProfileModal = true" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i> Edit Profil
                            </button>
                            <button @click="showChangePasswordModal = true" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                <i class="fas fa-lock mr-2"></i> Ganti Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-10 md:hidden">
            <div class="flex justify-around items-center h-16">
                <a href="../dashboard.php" class="flex flex-col items-center text-gray-500 hover:text-blue-600 text-sm" id="dashboardLinkBottomNav">
                    <i class="fas fa-home text-xl mb-1"></i>
                    Beranda
                </a>
                <a href="/WebGami/Siswa/content/history.php" class="flex flex-col items-center text-gray-500 hover:text-blue-600 text-sm">
                    <i class="fas fa-trophy text-xl mb-1"></i>
                    History
                </a>
                <a href="#" class="flex flex-col items-center text-blue-600 font-semibold text-sm">
                    <i class="fas fa-user-circle text-xl mb-1"></i>
                    Profil
                </a>
                <a href="../auth/logout.php" class="flex flex-col items-center text-gray-500 hover:text-red-600 text-sm">
                    <i class="fas fa-sign-out-alt text-xl mb-1"></i>
                    Logout
                </a>
            </div>
        </nav>

        <div x-show="showEditProfileModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-[10000] p-4">
            <div @click.away="showEditProfileModal = false" class="bg-white rounded-2xl shadow-2xl p-8 m-4 max-w-lg w-full transform transition-all duration-300 scale-100 opacity-100">
                <h3 class="text-3xl font-extrabold text-blue-700 mb-6 text-center">
                    <i class="fas fa-user-edit mr-3"></i>Edit Profil
                </h3>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit_profile">
                    <div class="mb-5">
                        <label for="edit_username" class="block text-gray-700 text-base font-semibold mb-2">Username:</label>
                        <input type="text" id="edit_username" name="username" value="<?php echo $username_siswa; ?>"
                                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-800 leading-tight
                                        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                    </div>
                    <div class="mb-5">
                        <label for="edit_nisn" class="block text-gray-700 text-base font-semibold mb-2">NISN:</label>
                        <input type="text" id="edit_nisn" name="nisn" value="<?php echo $nisn_siswa; ?>"
                                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-800 leading-tight
                                        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                    </div>
                    <div class="mb-5">
                        <label for="edit_kelas" class="block text-gray-700 text-base font-semibold mb-2">Kelas:</label>
                        <input type="text" id="edit_kelas" name="kelas" value="<?php echo $kelas_siswa; ?>"
                                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-800 leading-tight
                                        focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                    </div>
                    <div class="mb-6">
                        <label for="edit_foto" class="block text-gray-700 text-base font-semibold mb-2">Ganti Foto Profil:</label>
                        <input type="file" id="edit_foto" name="foto" accept="image/*"
                                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                                        file:rounded-lg file:border-0 file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                        transition duration-200 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-2">Biarkan kosong jika tidak ingin mengganti foto. (Max 2MB, JPG/PNG)</p>
                    </div>
                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                        <button type="button" @click="showEditProfileModal = false"
                                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 px-6 rounded-lg transition duration-200
                                            focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Batal
                        </button>
                        <button type="submit"
                                    class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold
                                            py-2.5 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-200
                                            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="showChangePasswordModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-[10000] p-4">
            <div @click.away="showChangePasswordModal = false" class="bg-white rounded-2xl shadow-2xl p-8 m-4 max-w-md w-full transform transition-all duration-300 scale-100 opacity-100">
                <h3 class="text-3xl font-extrabold text-green-700 mb-6 text-center">
                    <i class="fas fa-key mr-3"></i>Ganti Password
                </h3>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-5">
                        <label for="current_password" class="block text-gray-700 text-base font-semibold mb-2">Password Saat Ini:</label>
                        <input type="password" id="current_password" name="current_password" required
                                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-800 leading-tight
                                        focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                    </div>
                    <div class="mb-5">
                        <label for="new_password" class="block text-gray-700 text-base font-semibold mb-2">Password Baru:</label>
                        <input type="password" id="new_password" name="new_password" required
                                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-800 leading-tight
                                        focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                    </div>
                    <div class="mb-6">
                        <label for="confirm_password" class="block text-gray-700 text-base font-semibold mb-2">Konfirmasi Password Baru:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required
                                class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-800 leading-tight
                                        focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200">
                    </div>
                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                        <button type="button" @click="showChangePasswordModal = false"
                                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 px-6 rounded-lg transition duration-200
                                            focus:outline-none focus:ring-2 focus:ring-gray-400">
                            Batal
                        </button>
                        <button type="submit"
                                    class="bg-gradient-to-r from-green-500 to-teal-600 hover:from-green-600 hover:to-teal-700 text-white font-bold
                                            py-2.5 px-6 rounded-lg shadow-md hover:shadow-lg transition duration-200
                                            focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <i class="fas fa-check-circle mr-2"></i>Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        const notyf = new Notyf({
            duration: 3000,
            position: {
                x: 'right',
                y: 'top',
            },
            dismissible: true,
            ripple: true,
            types: [
                {
                    type: 'success',
                    background: '#28a745',
                    icon: {
                        className: 'fas fa-check-circle',
                        tagName: 'i',
                        color: 'white'
                    }
                },
                {
                    type: 'error',
                    background: '#dc3545',
                    icon: {
                        className: 'fas fa-times-circle',
                        tagName: 'i',
                        color: 'white'
                    }
                }
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

            const backButton = document.getElementById('backButton');
            if (backButton) {
                backButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    showLoader();

                    setTimeout(() => {
                        history.back();
                    }, 500);
                });
            }

            const dashboardLinkBottomNav = document.getElementById('dashboardLinkBottomNav');
            if (dashboardLinkBottomNav) {
                dashboardLinkBottomNav.addEventListener('click', function(event) {
                    event.preventDefault();
                    showLoader();
                    setTimeout(() => {
                        window.location.href = dashboardLinkBottomNav.href;
                    }, 500);
                });
            }

            const editFotoInput = document.getElementById('edit_foto');
            if (editFotoInput) {
                editFotoInput.addEventListener('change', function() {
                    const file = this.files[0];
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    const maxSize = 2 * 1024 * 1024;

                    if (file) {
                        if (!allowedTypes.includes(file.type)) {
                            notyf.error("Format foto tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.");
                            this.value = '';
                        } else if (file.size > maxSize) {
                            notyf.error("Ukuran foto terlalu besar (maks 2MB).");
                            this.value = '';
                        }
                    }
                });
            }
        });

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                hideLoader();
            }
        });

        window.addEventListener('beforeunload', showLoader);
    </script>
</body>
</html>