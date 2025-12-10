<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $nickname = isset($data['nickname']) ? trim($data['nickname']) : '';

    if (!$email) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Некорректный email']);
        exit;
    }

    if (empty($nickname)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Nickname обязателен']);
        exit;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Insert participant with email and nickname, quiz_score defaults to 0
        $stmt = $conn->prepare("INSERT INTO participants (email, nickname, quiz_score) VALUES (?, ?, 0)");
        $stmt->execute([$email, $nickname]);

        // Get the last inserted ID
        $participantId = $conn->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Участник успешно зарегистрирован',
            'participant_id' => (int)$participantId,
            'nickname' => $nickname
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Этот email уже зарегистрирован']);
        } else {
            http_response_code(500);
            error_log('Register participant error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка сервера'
            ]);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
}