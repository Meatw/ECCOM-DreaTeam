<?php
class Product {
    // Database connection and table name
    private $conn;
    private $table_name = "products";
    
    // Object properties
    public $id;
    public $seller_id;
    public $category_id;
    public $name;
    public $description;
    public $price;
    public $stock_quantity;
    public $image_url;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create product
    function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    seller_id=:seller_id,
                    category_id=:category_id,
                    name=:name,
                    description=:description,
                    price=:price,
                    stock_quantity=:stock_quantity,
                    image_url=:image_url,
                    created_at=:created_at,
                    updated_at=:updated_at";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->seller_id = htmlspecialchars(strip_tags($this->seller_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));
        
        // Bind values
        $stmt->bindParam(":seller_id", $this->seller_id);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":image_url", $this->image_url);
        $stmt->bindParam(":created_at", $this->created_at);
        $stmt->bindParam(":updated_at", $this->updated_at);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Read all products
    function readAll() {
        // Query to select all products
        $query = "SELECT p.id, p.seller_id, p.category_id, p.name, p.description, p.price, p.stock_quantity, p.image_url, p.created_at,
                        c.name as category_name, s.store_name as seller_name
                FROM " . $this->table_name . " p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN sellers s ON p.seller_id = s.id
                ORDER BY p.created_at DESC";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read products by category
    function readByCategory() {
        // Query to select products by category
        $query = "SELECT p.id, p.seller_id, p.category_id, p.name, p.description, p.price, p.stock_quantity, p.image_url, p.created_at,
                        c.name as category_name, s.store_name as seller_name
                FROM " . $this->table_name . " p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN sellers s ON p.seller_id = s.id
                WHERE p.category_id = ?
                ORDER BY p.created_at DESC";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind category ID
        $stmt->bindParam(1, $this->category_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read products by seller
    function readBySeller() {
        // Query to select products by seller
        $query = "SELECT p.id, p.seller_id, p.category_id, p.name, p.description, p.price, p.stock_quantity, p.image_url, p.created_at,
                        c.name as category_name
                FROM " . $this->table_name . " p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.seller_id = ?
                ORDER BY p.created_at DESC";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Bind seller ID
        $stmt->bindParam(1, $this->seller_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Read single product
    function readOne() {
        // Query to read single record
        $query = "SELECT p.id, p.seller_id, p.category_id, p.name, p.description, p.price, p.stock_quantity, p.image_url, p.created_at,
                        c.name as category_name, s.store_name as seller_name, s.user_id as seller_user_id
                FROM " . $this->table_name . " p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN sellers s ON p.seller_id = s.id
                WHERE p.id = ?
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
            $this->seller_id = $row['seller_id'];
            $this->category_id = $row['category_id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->image_url = $row['image_url'];
            $this->created_at = $row['created_at'];
            
            // Additional properties
            $this->category_name = $row['category_name'];
            $this->seller_name = $row['seller_name'];
            $this->seller_user_id = $row['seller_user_id'];
            
            return true;
        }
        
        return false;
    }
    
    // Update product
    function update() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    category_id = :category_id,
                    name = :name,
                    description = :description,
                    price = :price,
                    stock_quantity = :stock_quantity,
                    image_url = :image_url,
                    updated_at = :updated_at
                WHERE
                    id = :id AND seller_id = :seller_id";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->image_url = htmlspecialchars(strip_tags($this->image_url));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->seller_id = htmlspecialchars(strip_tags($this->seller_id));
        
        // Bind new values
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':stock_quantity', $this->stock_quantity);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':updated_at', $this->updated_at);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':seller_id', $this->seller_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete product
    function delete() {
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
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Search products
    function search($keywords) {
        // Query to search products
        $query = "SELECT p.id, p.seller_id, p.category_id, p.name, p.description, p.price, p.stock_quantity, p.image_url, p.created_at,
                        c.name as category_name, s.store_name as seller_name
                FROM " . $this->table_name . " p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN sellers s ON p.seller_id = s.id
                WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ? OR s.store_name LIKE ?
                ORDER BY p.created_at DESC";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";
        
        // Bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->bindParam(4, $keywords);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get trending products
    function getTrending() {
        // Query to get trending products (based on order count)
        $query = "SELECT p.id, p.seller_id, p.category_id, p.name, p.description, p.price, p.stock_quantity, p.image_url, p.created_at,
                        c.name as category_name, s.store_name as seller_name,
                        COUNT(oi.product_id) as order_count
                FROM " . $this->table_name . " p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN sellers s ON p.seller_id = s.id
                LEFT JOIN order_items oi ON p.id = oi.product_id
                GROUP BY p.id
                ORDER BY order_count DESC
                LIMIT 10";
        
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
}
?>