<?php
// CCDI Visitor Logging System

session_start();

// Database Credentials
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ccdi_visitor_esmena_db');
define('APP_NAME', 'CCDI Visitor Logging System');

class Database {
    private $conn;

    public function __construct() {
        try {
            $this->conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        return $this->conn->query($sql);
    }

    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    public function close() {
        $this->conn->close();
    }
}

$database = new Database();
$conn = $database->getConnection();
?>
