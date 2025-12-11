<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Get contest status (public endpoint)
$stmt = $conn->query("SELECT is_active FROM contest_settings WHERE id = 1");
$status = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'is_active' => (bool)$status['is_active']
]);
