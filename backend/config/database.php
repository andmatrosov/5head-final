<?php
class Database {
    private $db;

    public function __construct() {
        $dbPath = __DIR__ . '/../database/quiz.db';

        // Создаем директорию если не существует
        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0755, true);
        }

        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTables();
    }

    private function createTables() {
        // Create prizes table
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS prizes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                total_quantity INTEGER NOT NULL,
                available_quantity INTEGER NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Create participants table
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS participants (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT UNIQUE NOT NULL,
                nickname TEXT,
                quiz_answers TEXT,
                quiz_score INTEGER DEFAULT 0,
                is_winner BOOLEAN DEFAULT 0,
                prize_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (prize_id) REFERENCES prizes(id)
            )
        ");

        // Migration: Add quiz_score column if it doesn't exist
        $this->addColumnIfNotExists('participants', 'quiz_score', 'INTEGER DEFAULT 0');

        // Migration: Add prize_id column if it doesn't exist
        $this->addColumnIfNotExists('participants', 'prize_id', 'INTEGER');

        // Migration: Add nickname column if it doesn't exist
        $this->addColumnIfNotExists('participants', 'nickname', 'TEXT');
    }

    private function addColumnIfNotExists($table, $column, $definition) {
        try {
            // Check if column exists
            $result = $this->db->query("PRAGMA table_info($table)");
            $columns = $result->fetchAll(PDO::FETCH_ASSOC);

            $columnExists = false;
            foreach ($columns as $col) {
                if ($col['name'] === $column) {
                    $columnExists = true;
                    break;
                }
            }

            // Add column if it doesn't exist
            if (!$columnExists) {
                $this->db->exec("ALTER TABLE $table ADD COLUMN $column $definition");
            }
        } catch (PDOException $e) {
            // Ignore errors if column already exists
        }
    }

    public function getConnection() {
        return $this->db;
    }
}