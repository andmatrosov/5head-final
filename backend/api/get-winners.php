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

/**
 * Mask email for privacy
 * Example: kirill.petrov08@mail.ru -> kir***08@mail.ru
 */
function maskEmail($email) {
    $parts = explode('@', $email);
    if (count($parts) !== 2) {
        return $email;
    }

    $localPart = $parts[0];
    $domain = $parts[1];

    $length = strlen($localPart);

    if ($length <= 3) {
        // For very short emails, mask all but first character
        return substr($localPart, 0, 1) . '***@' . $domain;
    }

    // Take first 3 characters and last 2 characters, mask the middle
    $start = substr($localPart, 0, 3);
    $end = substr($localPart, -2);

    return $start . '***' . $end . '@' . $domain;
}

$db = new Database();
$conn = $db->getConnection();

// Get all winners with their prizes
$query = "SELECT p.email, p.quiz_score, pr.name as prize_name
          FROM participants p
          LEFT JOIN prizes pr ON p.prize_id = pr.id
          WHERE p.is_winner = 1
          ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$winners = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format response
$response = [];
foreach ($winners as $winner) {
    $response[] = [
        'email' => maskEmail($winner['email']),
        'score' => (int)$winner['quiz_score'],
        'prize' => $winner['prize_name'] ?? 'Приз не указан'
    ];
}

echo json_encode([
    'success' => true,
    'data' => $response
]);
