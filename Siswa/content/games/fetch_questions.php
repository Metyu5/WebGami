<?php
$base_dir = __DIR__ . '/../../..';
require_once $base_dir . '/config/koneksi.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$questions = [];
$soal_id = isset($_GET['soal_id']) ? intval($_GET['soal_id']) : 0;

try {
    if ($soal_id > 0) {
        $sql = "SELECT pertanyaan, jawaban FROM detail_soal WHERE soal_id = ?";
        $stmt = $koneksi->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception('Kueri SQL gagal disiapkan: ' . $koneksi->error);
        }
        
        $stmt->bind_param("i", $soal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $correctAnswer = intval($row['jawaban']);
                $pertanyaanText = $row['pertanyaan'];
                
                // Generate two random wrong answers
                $wrongAnswers = [];
                while (count($wrongAnswers) < 2) {
                    $wrong = $correctAnswer + rand(-10, 10);
                    if ($wrong !== $correctAnswer && !in_array($wrong, $wrongAnswers)) {
                        $wrongAnswers[] = $wrong;
                    }
                }
                
                $answers = array_merge([$correctAnswer], $wrongAnswers);
                shuffle($answers);

                $questions[] = [
                    'question' => $pertanyaanText,
                    'correctAnswer' => $correctAnswer,
                    'answers' => $answers,
                    'correctIndex' => array_search($correctAnswer, $answers)
                ];
            }
        }
        
        $stmt->close();
    } else {
        throw new Exception('Parameter soal_id tidak valid');
    }
    
    $koneksi->close();

    // Jika tidak ada soal yang ditemukan, kembalikan array kosong
    if (empty($questions)) {
        echo json_encode(["questions" => [], "count" => 0]);
        exit;
    }
    
    // Shuffle questions untuk variasi
    shuffle($questions);
    
    // Mengirimkan array pertanyaan dan jumlah total pertanyaan
    echo json_encode([
        "questions" => $questions,
        "count" => count($questions)
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>