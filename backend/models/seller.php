<?php
class Seller {
    // Database connection and table name
    private $conn;
    private $table_name = "sellers";
    
    // Object properties
    public $id;
    public $user_id;
    public $store_name;
    public $store_description;
    public $phone_number;
    public $is_approved;
    public $created_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create seller profile
    function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id=:user_id,
                    store_name=:store_name,
                    store_description=:store_description,
                    phone_number=:phone_number,
                    is_approved=:is_approved,
                    created_at=:created_at";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->store_name = htmlspecialchars(strip_tags($this->store_name));
        $this->store_description = htmlspecialchars(strip_tags($this->store_description));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        
        // Set default approval status to 0 (pending)
        $is_approved = 0;
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":store_name", $this->store_name);
        $stmt->bindParam(":store_description", $this->store_description);
        $stmt->bindParam(":phone_number", $this->phone_number);
        $stmt->bindParam(":is_approved", $is_approved);
        $stmt->bindParam(":created_at", $this->created_at);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Read seller profile by user ID
    function readByUserId() {
        // Query to read single record
        $query = "SELECT id, user_id, store_name, store_description, phone_number, is_approved, created_at
                FROM " . $this->table_name . "
                WHERE user_id = ?
                LIMIT 0,1";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind user ID
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        $stmt->execute();
        
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set values to object properties
        if ($row) {
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->store_name = $row['store_name'];
            $this->store_description = $row['store_description'];
            $this->phone_number = $row['phone_number'];
            $this->is_approved = $row['is_approved'];
            $this->created_at = $row['created_at'];
            return true;
        }
        
        return false;
    }
    
    // Update seller profile
    function update() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    store_name = :store_name,
                    store_description = :store_description,
                    phone_number = :phone_number
                WHERE
                    user_id = :user_id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->store_name = htmlspecialchars(strip_tags($this->store_name));
        $this->store_description = htmlspecialchars(strip_tags($this->store_description));
        $this->phone_number = htmlspecialchars(strip_tags($this->phone_number));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind new values
        $stmt->bindParam(':store_name', $this->store_name);
        $stmt->bindParam(':store_description', $this->store_description);
        $stmt->bindParam(':phone_number', $this->phone_number);
        $stmt->bindParam(':user_id', $this->user_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Approve seller
    function approve() {
        // Query to update approval status
        $query = "UPDATE " . $this->table_name . "
                SET
                    is_approved = 1
                WHERE
                    id = :id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind id
        $stmt->bindParam(':id', $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get all sellers
    function readAll() {
        // Query to select all sellers
        $query = "SELECT s.id, s.user_id, s.store_name, s.store_description, s.phone_number, s.is_approved, s.created_at,
                        u.full_name, u.email
                FROM " . $this->table_name . " s
                JOIN users u ON s.user_id = u.id
                ORDER BY s.created_at DESC";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get pending sellers
    function readPending() {
        // Query to select pending sellers
        $query = "SELECT s.id, s.user_id, s.store_name, s.store_description, s.phone_number, s.created_at,
                        u.full_name, u.email
                FROM " . $this->table_name . " s
                JOIN users u ON s.user_id = u.id
                WHERE s.is_approved = 0
                ORDER BY s.created_at DESC";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
}
?>