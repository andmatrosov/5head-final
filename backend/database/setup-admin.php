<?php
/**
 * Admin Users Table Setup Script
 * Run this once to create the admin_users table and add a default admin
 */

require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Create admin_users table
$sql = "CREATE TABLE IF NOT EXISTS admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

try {
    $conn->exec($sql);
    echo "✓ admin_users table created successfully\n";

    // Create default admin user (username: admin, password: admin123)
    // In production, change this password immediately!
    $username = '5HeadAdmin';
    $password = password_hash('majjep-1pipqi-zondIh', PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT OR IGNORE INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);

    echo "✓ Default admin user created (username: admin, password: admin123)\n";
    echo "⚠ IMPORTANT: Change the default password in production!\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}