<?php
require_once __DIR__ . '/environment.php';
/**
 * Flowwithlit Database Connection (PDO Singleton)
 * Secure, High-Performance MySQL Database Wrapper
 */

class Database {
    private static $instance = null;
    private $pdo;

    private $host;
    private $db;
    private $user;
    private $pass;
    private $port;
    private $charset = 'utf8mb4';

    private function __construct() {
        $this->host = getenv('FLOW_DB_HOST') ?: 'localhost';
        $this->db = getenv('FLOW_DB_NAME') ?: 'flowwithlit_db';
        $this->user = getenv('FLOW_DB_USER') ?: 'root';
        $this->pass = getenv('FLOW_DB_PASSWORD') ?: '';
        $this->port = getenv('FLOW_DB_PORT') ?: '3306';
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // Enforce real prepared statements to prevent SQL injection
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            // Log error internally without exposing database credentials to users
            error_log("Database Connection Error: " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error'   => 'Database Connection Error. Please verify local MySQL service is running.',
                'code'    => 'DB_CONN_ERROR'
            ]);
            exit();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
