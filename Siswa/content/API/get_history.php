<?php
session_start();
require_once '../../../config/koneksi.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'history' => []];

$siswaId = $_SESSION['siswaId'] ?? null;

if (!$siswaId) {
    $response['message'] = 'Session siswaId tidak ditemukan. Harap login.';
    echo json_encode($response);
    exit;
}

try {
    // Sesuaikan query dengan kolom yang ada di tabel HASIL_PERMAINAN
    $query = "SELECT id, skor, jawaban_benar, total_pertanyaan, tingkat_kesulitan, tanggal_main FROM hasil_permainan WHERE siswa_id = ? ORDER BY tanggal_main DESC LIMIT 10";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $siswaId);
    $stmt->execute();
    $result = $stmt->get_result();

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    $response['success'] = true;
    $response['history'] = $history;
    
    $stmt->close();
} catch (Exception $e) {
    $response['message'] = "Terjadi kesalahan database: " . $e->getMessage();
}

echo json_encode($response);
$koneksi->close();
?>