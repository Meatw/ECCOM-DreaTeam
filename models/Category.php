<?php
class Category {
    // Database connection and table name
    private $conn;
    private $table_name = "categories";

    // Object properties
    public $id;
    public $name;
    public $description;
    public $created;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create category
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    description = :description,
                    created = NOW()";
    
        // Prepare query
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
    
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // Read all categories
    public function readAll() {
        // Query to select all categories
        $query = "SELECT
                    id, name, description, created
                FROM
                    " . $this->table_name . "
                ORDER BY
                    name";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Execute query
        $stmt->execute();
    
        return $stmt;
    }
}
?>

