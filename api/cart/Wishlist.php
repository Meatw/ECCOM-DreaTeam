<?php
class Wishlist {
    // Database connection and table name
    private $conn;
    private $table_name = "wishlist_items";
    
    // Object properties
    public $id;
    public $user_id;
    public $product_id;
    public $created_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Add item to wishlist
    public function addItem() {
        // Check if item already exists in wishlist
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE user_id = ? AND product_id = ?";
        
        // Prepare query
        $check_stmt = $this->conn->prepare($check_query);
        
        // Bind values
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->bindParam(2, $this->product_id);
        
        // Execute query
        $check_stmt->execute();
        
        // If item exists, return true (already in wishlist)
        if ($check_stmt->rowCount() > 0) {
            return true;
        }
        
        // If item doesn't exist, insert new item
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id=:user_id,
                    product_id=:product_id";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":product_id", $this->product_id);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Remove item from wishlist
    public function removeItem() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ? AND user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind values
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->user_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get user's wishlist
    public function getUserWishlist() {
        // Query to get wishlist items with product details
        $query = "SELECT w.id, w.product_id, w.created_at,
                  p.name, p.price, p.image, p.stock_quantity, p.business_id,
                  b.name as business_name
                  FROM " . $this->table_name . " w
                  JOIN products p ON w.product_id = p.id
                  JOIN businesses b ON p.business_id = b.id
                  WHERE w.user_id = ? AND p.status = 'active'
                  ORDER BY w.created_at DESC";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind user ID
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Check if product is in wishlist
    public function isInWishlist() {
        // Query to check if product is in wishlist
        $query = "SELECT id FROM " . $this->table_name . " WHERE user_id = ? AND product_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind values
        $stmt->bindParam(1, $this->user_id);
        $stmt->bindParam(2, $this->product_id);
        
        // Execute query
        $stmt->execute();
        
        // Return true if product is in wishlist
        return ($stmt->rowCount() > 0);
    }
    
    // Get wishlist count
    public function getWishlistCount() {
        // Query to count wishlist items
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind user ID
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        $stmt->execute();
        
        // Get row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['count'];
    }
}
?>