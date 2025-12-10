<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../middleware/auth.php';

// Require authentication
requireAuth();

// Get request data
$data = json_decode(file_get_contents('php://input'), true);
$prizeId = $data['prize_id'] ?? null;

if (!$prizeId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Prize ID is required']);
    exit();
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Start transaction
    $conn->beginTransaction();

    // Check prize availability
    $stmt = $conn->prepare("SELECT * FROM prizes WHERE id = ?");
    $stmt->execute([$prizeId]);
    $prize = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prize) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Prize not found']);
        exit();
    }

    if ($prize['available_quantity'] <= 0) {
        $conn->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No more prizes available']);
        exit();
    }

    // Select random participant who is not already a winner
    $stmt = $conn->query("SELECT id FROM participants WHERE is_winner = 0 ORDER BY RANDOM() LIMIT 1");
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$participant) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'No eligible participants found']);
        exit();
    }

    // Mark participant as winner and assign prize
    $stmt = $conn->prepare("UPDATE participants SET is_winner = 1, prize_id = ? WHERE id = ?");
    $stmt->execute([$prizeId, $participant['id']]);

    // Decrease available prize quantity
    $stmt = $conn->prepare("UPDATE prizes SET available_quantity = available_quantity - 1 WHERE id = ?");
    $stmt->execute([$prizeId]);

    // Commit transaction
    $conn->commit();

    // Return the winner with prize info
    $stmt = $conn->prepare("SELECT p.*, pr.name as prize_name
                            FROM participants p
                            LEFT JOIN prizes pr ON p.prize_id = pr.id
                            WHERE p.id = ?");
    $stmt->execute([$participant['id']]);
    $winner = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'winner' => $winner,
        'prize' => [
            'id' => $prize['id'],
            'name' => $prize['name'],
            'remaining' => $prize['available_quantity'] - 1
        ]
    ]);

} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}