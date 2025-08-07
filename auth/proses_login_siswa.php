<?php
session_start();
include '../config/koneksi.php';

$nisn = $_POST['nisn'] ?? '';
$password = $_POST['password'] ?? '';
// BARIS INI TIDAK DIPERLUKAN UNTUK LOGIN
// $confirm_password = $_POST['confirm_password'] ?? '';
$kategori_form = $_POST['kategori'] ?? '';

// Validasi input hanya NISN dan Password
// HAPUS || empty($confirm_password) dari kondisi ini
if (empty($nisn) || empty($password)) {
    header("Location: ../index2.php?error=NISN dan password tidak boleh kosong"); // Ubah pesan error agar lebih spesifik
    exit();
}

// BARIS INI TIDAK DIPERLUKAN UNTUK LOGIN
// if ($password !== $confirm_password) {
//     header("Location: ../index2.php?error=Password dan konfirmasi tidak cocok");
//     exit();
// }


if ($kategori_form === 'siswa') {
    $stmt = $koneksi->prepare(
        "SELECT siswaId, username, nisn, password, kategori, foto FROM siswa WHERE nisn = ? LIMIT 1"
    );
} else {
    header("Location: ../index2.php?error=Kategori tidak dikenali");
    exit();
}
$stmt->bind_param("s", $nisn);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['siswaId'] = $user['siswaId'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['kategori'] = $user['kategori'];
    $_SESSION['foto'] = $user['foto'];

    // Opsional: Tambahkan error_log untuk debugging di server log
    error_log("DEBUG: Login Siswa BERHASIL. User: {$user['username']}, Foto: " . ($_SESSION['foto'] ?? 'NULL'));

    header("Location: ../siswa/dashboard.php?success=Selamat datang, {$user['username']}");
    exit();
} else {
    // Opsional: Tambahkan error_log untuk debugging login gagal
    error_log("DEBUG: Login Siswa GAGAL untuk NISN: {$nisn}");
    header("Location: ../index2.php?error=NISN atau password salah");
    exit();
}

// Tutup koneksi
$koneksi->close();
?>