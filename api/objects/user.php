<?php
class User {
 
    private $conn;
    private $table_name = "users";
 
    public $id;
    public $user;
    public $email;
    public $password;
  
    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
    
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user = :user,
                    email = :email,
                    password = :password";
    
        $stmt = $this->conn->prepare($query);

        $this->user=htmlspecialchars(strip_tags($this->user));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
    
        $stmt->bindParam(':user', $this->user);
        $stmt->bindParam(':email', $this->email);
    
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);

        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }
    function emailExists(){
        $query = "SELECT id, user, password
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        $stmt = $this->conn->prepare( $query ); 
        $this->email=htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->user = $row['user'];
            $this->password = $row['password']; 
            return true;
        } 
        return false;
    }

    // check exists user in DB 
    function userExists(){
        $query = "SELECT id, email, password
                FROM " . $this->table_name . "
                WHERE user = ?
                LIMIT 0,1";
    
        $stmt = $this->conn->prepare( $query ); 
        $this->user=htmlspecialchars(strip_tags($this->user));
        $stmt->bindParam(1, $this->user);
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->password = $row['password']; 
            return true;
        } 
        return false;
    }

    function blockUser($CurUser){
        $database = new Database();
        $db = $database->getConnection();
        //$stmt = $db->query("SELECT * FROM users WHERE email = $CurUser AND block=1");
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `users`.`email` = ? AND `users`.`block`=1");
        $stmt->bindParam(1, $CurUser);
        $stmt->execute();
        $result_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($result_array)>0) {
            return true;
        } 
        return false;
    }
}