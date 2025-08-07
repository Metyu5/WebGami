<?php
session_start();
include '../config/koneksi.php';  // Pastikan ini benar-benar ada dan koneksi berhasil

// Ambil data dari form
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$kategori_form = $_POST['kategori'] ?? '';  // Kategori
$confirm_password = $_POST['confirm-password'] ?? '';  // Konfirmasi password

// Validasi input
if (empty($email) || empty($password) || empty($kategori_form) || empty($confirm_password)) {
    $_SESSION['error'] = "Email, password, dan peran tidak boleh kosong.";
    header("Location: ../index.php?error=input_kosong");
    exit();
}

// Validasi konfirmasi password
if ($password !== $confirm_password) {
    $_SESSION['error'] = "Password dan konfirmasi password tidak cocok.";
    header("Location: ../index.php?error=password_mismatch");
    exit();
}

// Pastikan koneksi berhasil
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Query untuk mencari pengguna berdasarkan email
if ($kategori_form === 'admin') {
    $stmt = $koneksi->prepare("SELECT adminId AS id, username, email, password, kategori FROM admin WHERE email = ? LIMIT 1");
} elseif ($kategori_form === 'wali kelas') {
    $stmt = $koneksi->prepare("SELECT walkesId AS id, username, email, password, kategori FROM walikelas WHERE email = ? LIMIT 1");
} elseif ($kategori_form === 'siswa') {
    $stmt = $koneksi->prepare("SELECT siswaId AS id, username, email, password, kategori FROM siswa WHERE email = ? LIMIT 1");
} else {
    $_SESSION['error'] = "Kategori tidak dikenali.";
    header("Location: ../index.php?error=kategori_tidak_dikenal");
    exit();
}


$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Jika data ditemukan
$user = $result->fetch_assoc();
$stmt->close();  // Hanya tutup statement setelah selesai digunakan

// Verifikasi password dan kategori
if ($user && $password === $user['password']) {
    // Verifikasi kategori
    if ($kategori_form === $user['kategori']) {
        $_SESSION['adminId'] = $user['adminId'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['kategori'] = $user['kategori'];

        // Redirect ke dashboard sesuai kategori
        if ($user['kategori'] === 'admin') {
            $_SESSION['success'] = "Selamat datang kembali, " . $user['username'];
            header("Location: ../admin/pages/dashboard.php");
            exit();
        } elseif ($user['kategori'] === 'wali kelas') {
            $_SESSION['success'] = "Selamat datang kembali, " . $user['username'];
            header("Location: ../Walkes/pages/index.php");
            exit();
        } elseif ($user['kategori'] === 'siswa') {
            $_SESSION['success'] = "Selamat datang kembali, " . $user['username'];
            header("Location: ../siswa/pages/dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Kategori pengguna tidak dikenal.";
            header("Location: ../index.php?error=kategori_tidak_dikenal");
            exit();
        }
    } else {
        $_SESSION['error'] = "Peran yang Anda pilih tidak sesuai dengan data pengguna.";
        header("Location: ../index.php?error=kategori_salah");
        exit();
    }
} else {
    $_SESSION['error'] = "Email atau password salah.";
    header("Location: ../index.php?error=login_gagal");
    exit();
}

// Tutup koneksi database setelah semuanya selesai
$koneksi->close();
?>
