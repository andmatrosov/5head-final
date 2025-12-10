<?php
/**
 * Prizes Setup Script
 * Run this once to populate the prizes table with available prizes
 */

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Define prizes
$prizes = [
    ['name' => 'Talon Knife', 'quantity' => 1],
    ['name' => 'Skeleton Knife', 'quantity' => 1],
    ['name' => 'AK-47 | Asiimov', 'quantity' => 3],
    ['name' => 'M4A1-S | Nightmare', 'quantity' => 5],
    ['name' => '$5 Promocode', 'quantity' => 5]
];

try {
    // Check if prizes already exist
    $stmt = $conn->query("SELECT COUNT(*) as count FROM prizes");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        echo "⚠ Prizes already exist in database. Skipping...\n";
        echo "   To reset prizes, manually delete them from the database first.\n";
        exit();
    }

    // Insert prizes
    $stmt = $conn->prepare("INSERT INTO prizes (name, total_quantity, available_quantity) VALUES (?, ?, ?)");

    foreach ($prizes as $prize) {
        $stmt->execute([$prize['name'], $prize['quantity'], $prize['quantity']]);
        echo "✓ Added prize: {$prize['name']} ({$prize['quantity']} pcs)\n";
    }

    echo "\n✓ All prizes have been added successfully!\n";
    echo "\nAvailable prizes:\n";
    $stmt = $conn->query("SELECT * FROM prizes ORDER BY id");
    $allPrizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allPrizes as $prize) {
        echo "  - {$prize['name']}: {$prize['available_quantity']}/{$prize['total_quantity']} available\n";
    }

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}