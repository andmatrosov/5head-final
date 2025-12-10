<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $answers = json_encode($data['answers'] ?? []);
    $quizScore = isset($data['quiz_score']) ? intval($data['quiz_score']) : 0;

    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Некорректный email']);
        exit;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO participants (email, quiz_answers, quiz_score) VALUES (?, ?, ?)");
        $stmt->execute([$email, $answers, $quizScore]);

        echo json_encode(['success' => true, 'message' => 'Email успешно сохранен']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Этот email уже участвует']);
        } else {
            // Log error for debugging
            error_log('Submit email error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка сервера',
                'debug' => $e->getMessage() // Remove this in production
            ]);
        }
    }
}