<?php
class Product {
    // Database connection and table name
    private $conn;
    private $table_name = "products";

    // Object properties
    public $id;
    public $name;
    public $description;
    public $price;
    public $category_id;
    public $seller_id;
    public $image;
    public $created;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create product
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name = :name,
                    description = :description,
                    price = :price,
                    category_id = :category_id,
                    seller_id = :seller_id,
                    image = :image,
                    created = NOW()";
    
        // Prepare query
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->seller_id = htmlspecialchars(strip_tags($this->seller_id));
        $this->image = htmlspecialchars(strip_tags($this->image));
    
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":seller_id", $this->seller_id);
        $stmt->bindParam(":image", $this->image);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // Read all products
    public function readAll() {
        // Query to select all products
        $query = "SELECT
                    p.id, p.name, p.description, p.price, p.image, p.created,
                    c.name as category_name, u.username as seller_name
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN
                        categories c ON p.category_id = c.id
                    LEFT JOIN
                        users u ON p.seller_id = u.id
                ORDER BY
                    p.created DESC";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Execute query
        $stmt->execute();
    
        return $stmt;
    }

    // Read products by seller
    public function readBySeller() {
        // Query to select products by seller
        $query = "SELECT
                    p.id, p.name, p.description, p.price, p.image, p.created,
                    c.name as category_name
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN
                        categories c ON p.category_id = c.id
                WHERE
                    p.seller_id = ?
                ORDER BY
                    p.created DESC";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Bind seller ID
        $stmt->bindParam(1, $this->seller_id);
    
        // Execute query
        $stmt->execute();
    
        return $stmt;
    }

    // Read one product
    public function readOne() {
        // Query to read single record
        $query = "SELECT
                    p.id, p.name, p.description, p.price, p.category_id, p.seller_id, p.image, p.created,
                    c.name as category_name, u.username as seller_name
                FROM
                    " . $this->table_name . " p
                    LEFT JOIN
                        categories c ON p.category_id = c.id
                    LEFT JOIN
                        users u ON p.seller_id = u.id
                WHERE
                    p.id = ?
                LIMIT
                    0,1";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Bind id of product to be updated
        $stmt->bindParam(1, $this->id);
    
        // Execute query
        $stmt->execute();
    
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if($row) {
            // Set values to object properties
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->category_id = $row['category_id'];
            $this->seller_id = $row['seller_id'];
            $this->image = $row['image'];
            $this->created = $row['created'];
            return true;
        }
        
        return false;
    }

    // Update product
    public function update() {
        // Query to update record
        $query = "UPDATE
                    " . $this->table_name . "
                SET
                    name = :name,
                    description = :description,
                    price = :price,
                    category_id = :category_id,
                    image = :image
                WHERE
                    id = :id AND seller_id = :seller_id";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->image = htmlspecialchars(strip_tags($this->image));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->seller_id = htmlspecialchars(strip_tags($this->seller_id));
    
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":seller_id", $this->seller_id);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // Delete product
    public function delete() {
        // Query to delete product
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ? AND seller_id = ?";
    
        // Prepare query
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->seller_id = htmlspecialchars(strip_tags($this->seller_id));
    
        // Bind ids
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->seller_id);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }
}
?>

