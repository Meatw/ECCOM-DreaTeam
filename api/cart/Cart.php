<?php
class Cart {
    // Database connection and table name
    private $conn;
    private $table_name = "cart_items";
    
    // Object properties
    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Add item to cart
    public function addItem() {
        // Check if item already exists in cart
        $check_query = "SELECT id, quantity FROM " . $this->table_name . " WHERE user_id = ? AND product_id = ?";
        
        // Prepare query
        $check_stmt = $this->conn->prepare($check_query);
        
        // Bind values
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->bindParam(2, $this->product_id);
        
        // Execute query
        $check_stmt->execute();
        
        // If item exists, update quantity
        if ($check_stmt->rowCount() > 0) {
            $row = $check_stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $new_quantity = $row['quantity'] + $this->quantity;
            
            // Update query
            $query = "UPDATE " . $this->table_name . " SET quantity = ? WHERE id = ?";
            
            // Prepare query
            $stmt = $this->conn->prepare($query);
            
            // Bind values
            $stmt->bindParam(1, $new_quantity);
            $stmt->bindParam(2, $this->id);
            
            // Execute query
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
        }
        
        // If item doesn't exist, insert new item
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id=:user_id,
                    product_id=:product_id,
                    quantity=:quantity";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":quantity", $this->quantity);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Update cart item quantity
    public function updateQuantity() {
        // Query to update quantity
        $query = "UPDATE " . $this->table_name . " SET quantity = ? WHERE id = ? AND user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind values
        $stmt->bindParam(1, $this->quantity);
        $stmt->bindParam(2, $this->id);
        $stmt->bindParam(3, $this->user_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Remove item from cart
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
    
    // Get user's cart
    public function getUserCart() {
        // Query to get cart items with product details
        $query = "SELECT c.id, c.product_id, c.quantity, c.created_at,
                  p.name, p.price, p.image, p.stock_quantity, p.business_id,
                  b.name as business_name
                  FROM " . $this->table_name . " c
                  JOIN products p ON c.product_id = p.id
                  JOIN businesses b ON p.business_id = b.id
                  WHERE c.user_id = ? AND p.status = 'active'
                  ORDER BY c.created_at DESC";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind user ID
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Clear user's cart
    public function clearCart() {
        // Query to delete all user's cart items
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind user ID
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get cart count
    public function getCartCount() {
        // Query to count cart items
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