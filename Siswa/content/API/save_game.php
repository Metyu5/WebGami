<?php
session_start();
require_once '../../../config/koneksi.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$response = ['success' => false, 'message' => ''];

if ($data === null) {
    $response['message'] = 'Invalid JSON input.';
    echo json_encode($response);
    exit;
}

// Mengambil siswaId dari session
$siswaId = $_SESSION['siswaId'] ?? null;

// Mengambil data lainnya dari body JSON, termasuk soalId yang baru
$score = $data['score'] ?? 0;
$correctAnswers = $data['correctAnswers'] ?? 0;
$totalQuestions = $data['totalQuestions'] ?? 0;
$difficulty = $data['difficulty'] ?? 'Easy';
$soalId = $data['soalId'] ?? null; // Perbaikan: Ambil soalId dari body JSON

if (!$siswaId || !is_numeric($score) || !is_numeric($correctAnswers) || !is_numeric($totalQuestions) || !$soalId) {
    $response['message'] = 'Data tidak lengkap atau tidak valid. Mungkin session siswaId tidak ditemukan.';
    echo json_encode($response);
    exit;
}

try {
    $query = "INSERT INTO hasil_permainan (siswa_id, soal_id, skor, jawaban_benar, total_pertanyaan, tingkat_kesulitan) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($query);

    // Perbaiki bind_param: tambahkan 'i' untuk soalId
    // i: integer (siswa_id)
    // i: integer (soal_id)
    // i: integer (skor)
    // i: integer (jawaban_benar)
    // i: integer (total_pertanyaan)
    // s: string (tingkat_kesulitan)
    $stmt->bind_param("iiiiis", $siswaId, $soalId, $score, $correctAnswers, $totalQuestions, $difficulty);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Data berhasil disimpan.';
    } else {
        throw new Exception($stmt->error);
    }
    
    $stmt->close();

} catch (Exception $e) {
    $response['message'] = "Terjadi kesalahan database: " . $e->getMessage();
}

echo json_encode($response);
$koneksi->close();
?>