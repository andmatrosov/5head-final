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
require_once '../middleware/auth.php';

// Require authentication
requireAuth();

$db = new Database();
$conn = $db->getConnection();

// Get query parameters for filtering and sorting
$sortBy = $_GET['sortBy'] ?? 'created_at'; // created_at, quiz_score
$sortOrder = $_GET['sortOrder'] ?? 'DESC'; // ASC, DESC
$minScore = isset($_GET['minScore']) ? intval($_GET['minScore']) : null;
$maxScore = isset($_GET['maxScore']) ? intval($_GET['maxScore']) : null;
$dateFrom = $_GET['dateFrom'] ?? null;
$dateTo = $_GET['dateTo'] ?? null;

// Validate sort parameters
$allowedSortFields = ['created_at', 'quiz_score', 'email', 'is_winner'];
if (!in_array($sortBy, $allowedSortFields)) {
    $sortBy = 'created_at';
}

$sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

// Build query with filters
$query = "SELECT p.*, pr.name as prize_name
          FROM participants p
          LEFT JOIN prizes pr ON p.prize_id = pr.id
          WHERE 1=1";

$params = [];

// Score filters
if ($minScore !== null) {
    $query .= " AND p.quiz_score >= ?";
    $params[] = $minScore;
}

if ($maxScore !== null) {
    $query .= " AND p.quiz_score <= ?";
    $params[] = $maxScore;
}

// Date filters
if ($dateFrom) {
    $query .= " AND p.created_at >= ?";
    $params[] = $dateFrom;
}

if ($dateTo) {
    $query .= " AND p.created_at <= ?";
    $params[] = $dateTo;
}

// Add sorting
$query .= " ORDER BY p.$sortBy $sortOrder";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $participants
]);