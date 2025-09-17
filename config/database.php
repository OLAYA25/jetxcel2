<?php
/**
 * JETXCEL - Database Configuration
 * Database connection settings for MySQL/MariaDB
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'jetxcel_db';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed: " . $exception->getMessage());
        }
        
        return $this->conn;
    }

    public function closeConnection() {
        $this->conn = null;
    }
}

// Helper function for quick database connection
function getDBConnection() {
    $database = new Database();
    return $database->getConnection();
}

?>
