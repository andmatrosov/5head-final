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
$isActive = $data['is_active'] ? 1 : 0;

$db = new Database();
$conn = $db->getConnection();

try {
    // Update contest status
    $stmt = $conn->prepare("UPDATE contest_settings SET is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = 1");
    $stmt->execute([$isActive]);

    echo json_encode([
        'success' => true,
        'is_active' => (bool)$isActive,
        'message' => $isActive ? 'Конкурс активирован' : 'Конкурс завершен'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка сервера: ' . $e->getMessage()
    ]);
}
