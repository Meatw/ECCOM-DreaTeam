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
    public $created;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Add item to cart
    public function addItem() {
        // Check if item already exists in cart
        $check_query = "SELECT id, quantity FROM " . $this->table_name . " 
                        WHERE user_id = ? AND product_id = ?";
        
        // Prepare query
        $check_stmt = $this->conn->prepare($check_query);
        
        // Bind values
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->bindParam(2, $this->product_id);
        
        // Execute query
        $check_stmt->execute();
        
        // If item exists, update quantity
        if($check_stmt->rowCount() > 0) {
            $row = $check_stmt->fetch(PDO::FETCH_ASSOC);
            $current_quantity = $row['quantity'];
            $new_quantity = $current_quantity + $this->quantity;
            
            // Update query
            $query = "UPDATE " . $this->table_name . "
                    SET quantity = :quantity
                    WHERE id = :id";
            
            // Prepare query
            $stmt = $this->conn->prepare($query);
            
            // Bind values
            $stmt->bindParam(":quantity", $new_quantity);
            $stmt->bindParam(":id", $row['id']);
            
            // Execute query
            if($stmt->execute()) {
                return true;
            }
            
            return false;
        }
        
        // If item doesn't exist, add new item
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id = :user_id,
                    product_id = :product_id,
                    quantity = :quantity,
                    created = NOW()";
        
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
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Get cart items
    public function getItems() {
        // Query to get cart items with product details
        $query = "SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.image
                FROM " . $this->table_name . " c
                LEFT JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind value
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }

    // Remove item from cart
    public function removeItem() {
        // Query to delete cart item
        $query = "DELETE FROM " . $this->table_name . " 
                WHERE id = ? AND user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind values
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->user_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Clear cart
    public function clearCart() {
        // Query to delete all cart items for a user
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind value
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>

