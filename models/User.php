<?php
class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";

    // Object properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $role; // 'customer', 'seller', 'admin'
    public $created;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Register user
    public function register() {
        // Sanitize inputs
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->role = htmlspecialchars(strip_tags($this->role));
        
        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password = :password,
                    role = :role,
                    created = NOW()";
    
        // Prepare query
        $stmt = $this->conn->prepare($query);
    
        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":role", $this->role);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // Login user
    public function login() {
        // Query to read single record
        $query = "SELECT id, username, email, password, role
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Bind email
        $stmt->bindParam(1, $this->email);
    
        // Execute query
        $stmt->execute();
    
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // If email exists, verify password
        if($row) {
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            
            // Verify password
            if(password_verify($this->password, $row['password'])) {
                return true;
            }
        }
    
        return false;
    }

    // Get user by ID
    public function readOne() {
        // Query to read single record
        $query = "SELECT id, username, email, role, created
                FROM " . $this->table_name . "
                WHERE id = ?
                LIMIT 0,1";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Bind ID
        $stmt->bindParam(1, $this->id);
    
        // Execute query
        $stmt->execute();
    
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if($row) {
            // Set values to object properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->created = $row['created'];
            return true;
        }
        
        return false;
    }

    // Admin: Get all users
    public function readAll() {
        // Query to select all users
        $query = "SELECT id, username, email, role, created
                FROM " . $this->table_name . "
                ORDER BY created DESC";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Execute query
        $stmt->execute();
    
        return $stmt;
    }

    // Admin: Delete user
    public function delete() {
        // Query to delete user
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
    
        // Prepare query
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // Bind id
        $stmt->bindParam(1, $this->id);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }
}
?>

