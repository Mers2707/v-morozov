<?php
// MySQL DB 
class Database {
 
    // DB account 
    private $host = "localhost";
    private $db_name = "api_db";
    private $username = "mysql";
    private $password = "admindb";
    public $conn;
 
    // DB connection 
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>