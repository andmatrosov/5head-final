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

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$isWinner = $data['is_winner'] ? 1 : 0;
$prizeId = $data['prize_id'] ?? null;

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->beginTransaction();

    // Get current participant state
    $stmt = $conn->prepare("SELECT is_winner, prize_id FROM participants WHERE id = ?");
    $stmt->execute([$id]);
    $currentState = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentState) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Participant not found']);
        exit();
    }

    // If removing winner status, free up the prize
    if (!$isWinner && $currentState['is_winner'] && $currentState['prize_id']) {
        // Increase available quantity back
        $stmt = $conn->prepare("UPDATE prizes SET available_quantity = available_quantity + 1 WHERE id = ?");
        $stmt->execute([$currentState['prize_id']]);
    }

    // If marking as winner with a prize
    if ($isWinner && $prizeId) {
        // Check prize availability
        $stmt = $conn->prepare("SELECT available_quantity FROM prizes WHERE id = ?");
        $stmt->execute([$prizeId]);
        $prize = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prize) {
            $conn->rollBack();
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Prize not found']);
            exit();
        }

        // If changing prize, free up old prize
        if ($currentState['prize_id'] && $currentState['prize_id'] != $prizeId) {
            $stmt = $conn->prepare("UPDATE prizes SET available_quantity = available_quantity + 1 WHERE id = ?");
            $stmt->execute([$currentState['prize_id']]);
        }

        // If new prize (not just toggling existing)
        if (!$currentState['prize_id'] || $currentState['prize_id'] != $prizeId) {
            if ($prize['available_quantity'] <= 0) {
                $conn->rollBack();
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No more prizes available']);
                exit();
            }

            // Decrease available quantity
            $stmt = $conn->prepare("UPDATE prizes SET available_quantity = available_quantity - 1 WHERE id = ?");
            $stmt->execute([$prizeId]);
        }

        // Update participant
        $stmt = $conn->prepare("UPDATE participants SET is_winner = ?, prize_id = ? WHERE id = ?");
        $stmt->execute([$isWinner, $prizeId, $id]);
    } else {
        // Update participant without prize change (or removing winner status)
        $newPrizeId = $isWinner ? $currentState['prize_id'] : null;
        $stmt = $conn->prepare("UPDATE participants SET is_winner = ?, prize_id = ? WHERE id = ?");
        $stmt->execute([$isWinner, $newPrizeId, $id]);
    }

    $conn->commit();

    // Return updated participant
    $stmt = $conn->prepare("SELECT p.*, pr.name as prize_name
                            FROM participants p
                            LEFT JOIN prizes pr ON p.prize_id = pr.id
                            WHERE p.id = ?");
    $stmt->execute([$id]);
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'participant' => $participant]);

} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}