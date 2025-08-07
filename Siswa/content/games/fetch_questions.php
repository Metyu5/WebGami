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
                // Coba mencocokkan format matematika dengan berbagai operator
                $parts = preg_match('/(\d+)\s*([+\-×x*÷\/])\s*(\d+)/', $row['pertanyaan'], $matches);
                
                if ($parts) {
                    // Jika cocok dengan format matematika
                    $operator = $matches[2];
                    
                    // Normalisasi operator
                    switch ($operator) {
                        case 'x':
                        case 'X':
                        case '*':
                            $displayOperator = '×';
                            break;
                        case '÷':
                        case '/':
                            $displayOperator = '÷';
                            break;
                        default:
                            $displayOperator = $operator;
                    }
                    
                    $questions[] = [
                        'bilangan1' => intval($matches[1]),
                        'bilangan2' => intval($matches[3]),
                        'operator' => $displayOperator,
                        'jawaban_benar' => intval($row['jawaban'])
                    ];
                } else {
                    // Jika tidak, asumsikan itu pertanyaan teks
                    $questions[] = [
                        'pertanyaan' => $row['pertanyaan'],
                        'jawaban_benar' => intval($row['jawaban'])
                    ];
                }
            }
        } else {
            // Jika tidak ada data dari database, berikan data fallback
            $fallbackQuestions = [
                '1' => [ // Penjumlahan
                    ['bilangan1' => 5, 'bilangan2' => 3, 'operator' => '+', 'jawaban_benar' => 8],
                    ['bilangan1' => 7, 'bilangan2' => 4, 'operator' => '+', 'jawaban_benar' => 11],
                    ['bilangan1' => 9, 'bilangan2' => 6, 'operator' => '+', 'jawaban_benar' => 15],
                    ['bilangan1' => 12, 'bilangan2' => 8, 'operator' => '+', 'jawaban_benar' => 20],
                    ['bilangan1' => 15, 'bilangan2' => 5, 'operator' => '+', 'jawaban_benar' => 20]
                ],
                '2' => [ // Pengurangan
                    ['bilangan1' => 10, 'bilangan2' => 3, 'operator' => '-', 'jawaban_benar' => 7],
                    ['bilangan1' => 15, 'bilangan2' => 7, 'operator' => '-', 'jawaban_benar' => 8],
                    ['bilangan1' => 20, 'bilangan2' => 12, 'operator' => '-', 'jawaban_benar' => 8],
                    ['bilangan1' => 18, 'bilangan2' => 9, 'operator' => '-', 'jawaban_benar' => 9],
                    ['bilangan1' => 25, 'bilangan2' => 15, 'operator' => '-', 'jawaban_benar' => 10]
                ]
            ];
            
            if (isset($fallbackQuestions[$soal_id])) {
                $questions = $fallbackQuestions[$soal_id];
            }
        }
        
        $stmt->close();
    } else {
        throw new Exception('Parameter soal_id tidak valid');
    }
    
    $koneksi->close();
    
    // Shuffle questions untuk variasi
    if (!empty($questions)) {
        shuffle($questions);
    }
    
    if (empty($questions)) {
        throw new Exception('Tidak ada soal ditemukan untuk ID: ' . $soal_id);
    }
    
    echo json_encode($questions);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>