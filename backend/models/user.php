<?php
class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";
    
    // Object properties
    public $id;
    public $full_name;
    public $email;
    public $password;
    public $account_type;
    public $created_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create user
    function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    full_name=:full_name,
                    email=:email,
                    password=:password,
                    account_type=:account_type,
                    created_at=:created_at";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->account_type = htmlspecialchars(strip_tags($this->account_type));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        
        // Bind values
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":account_type", $this->account_type);
        $stmt->bindParam(":created_at", $this->created_at);
        
        // Execute query
        if ($stmt->execute()) {
            // Get the ID of the newly created user
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Login user
    function login() {
        // Query to check if email exists
        $query = "SELECT id, full_name, email, password, account_type
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind email value
        $stmt->bindParam(1, $this->email);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Check if email exists
    function emailExists($email) {
        // Query to check if email exists
        $query = "SELECT id
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $email = htmlspecialchars(strip_tags($email));
        
        // Bind email value
        $stmt->bindParam(1, $email);
        
        // Execute query
        $stmt->execute();
        
        // Get number of rows
        $num = $stmt->rowCount();
        
        // If email exists, return true
        if ($num > 0) {
            return true;
        }
        
        return false;
    }
    
    // Get user by ID
    function readOne() {
        // Query to read single record
        $query = "SELECT id, full_name, email, account_type, created_at
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
        
        // Set values to object properties
        if ($row) {
            $this->id = $row['id'];
            $this->full_name = $row['full_name'];
            $this->email = $row['email'];
            $this->account_type = $row['account_type'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }
    
    // Update user
    function update() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    full_name = :full_name,
                    email = :email
                WHERE
                    id = :id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind new values
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Update password
    function updatePassword() {
        // Query to update password
        $query = "UPDATE " . $this->table_name . "
                SET
                    password = :password
                WHERE
                    id = :id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind new values
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':id', $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete user
    function delete() {
        // Query to delete user
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind id
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>