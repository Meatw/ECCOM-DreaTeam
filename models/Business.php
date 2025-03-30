<?php
class Business {
    // Database connection and table name
    private $conn;
    private $table_name = "businesses";

    // Object properties
    public $id;
    public $user_id;
    public $name;
    public $description;
    public $status; // 'pending', 'approved', 'rejected'
    public $created;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Register business
    public function register() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id = :user_id,
                    name = :name,
                    description = :description,
                    status = 'pending',
                    created = NOW()";
    
        // Prepare query
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
    
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // Admin: Get pending businesses
    public function getPendingBusinesses() {
        // Query to select pending businesses
        $query = "SELECT
                    b.id, b.name, b.description, b.created,
                    u.username, u.email
                FROM
                    " . $this->table_name . " b
                    LEFT JOIN
                        users u ON b.user_id = u.id
                WHERE
                    b.status = 'pending'
                ORDER BY
                    b.created ASC";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Execute query
        $stmt->execute();
    
        return $stmt;
    }

    // Admin: Approve/Reject business
    public function updateStatus() {
        // Query to update business status
        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status
                WHERE
                    id = :id";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // Bind values
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // Get business by ID
    public function readOne() {
        // Query to read single record
        $query = "SELECT
                    id, user_id, name, description, status, created
                FROM
                    " . $this->table_name . "
                WHERE
                    id = ?
                LIMIT
                    0,1";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Bind id of business to be read
        $stmt->bindParam(1, $this->id);
    
        // Execute query
        $stmt->execute();
    
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if($row) {
            // Set values to object properties
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->status = $row['status'];
            $this->created = $row['created'];
            return true;
        }
        
        return false;
    }
}
?>

