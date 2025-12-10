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

    $participantId = isset($data['participant_id']) ? intval($data['participant_id']) : 0;
    $quizScore = isset($data['quiz_score']) ? intval($data['quiz_score']) : 0;
    $quizAnswers = isset($data['quiz_answers']) ? json_encode($data['quiz_answers']) : null;

    if ($participantId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Некорректный ID участника']);
        exit;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Check if participant exists
        $checkStmt = $conn->prepare("SELECT id FROM participants WHERE id = ?");
        $checkStmt->execute([$participantId]);

        if (!$checkStmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Участник не найден']);
            exit;
        }

        // Update score and optionally answers
        if ($quizAnswers !== null) {
            $stmt = $conn->prepare("UPDATE participants SET quiz_score = ?, quiz_answers = ? WHERE id = ?");
            $stmt->execute([$quizScore, $quizAnswers, $participantId]);
        } else {
            $stmt = $conn->prepare("UPDATE participants SET quiz_score = ? WHERE id = ?");
            $stmt->execute([$quizScore, $participantId]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Очки успешно обновлены',
            'participant_id' => $participantId,
            'quiz_score' => $quizScore
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        error_log('Update score error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка сервера'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
}