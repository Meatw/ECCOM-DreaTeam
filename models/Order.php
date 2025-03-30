<?php
class Order {
    // Database connection and table names
    private $conn;
    private $table_name = "orders";
    private $items_table = "order_items";

    // Object properties
    public $id;
    public $user_id;
    public $total_amount;
    public $status; // 'pending', 'approved', 'shipped', 'delivered'
    public $created;
    public $items = array(); // Array of order items

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create order
    public function create() {
        // Start transaction
        $this->conn->beginTransaction();
        
        try {
            // Insert order
            $query = "INSERT INTO " . $this->table_name . "
                    SET
                        user_id = :user_id,
                        total_amount = :total_amount,
                        status = 'pending',
                        created = NOW()";
            
            // Prepare query
            $stmt = $this->conn->prepare($query);
            
            // Sanitize
            $this->user_id = htmlspecialchars(strip_tags($this->user_id));
            $this->total_amount = htmlspecialchars(strip_tags($this->total_amount));
            
            // Bind values
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":total_amount", $this->total_amount);
            
            // Execute query
            $stmt->execute();
            
            // Get order ID
            $this->id = $this->conn->lastInsertId();
            
            // Insert order items
            foreach($this->items as $item) {
                $query = "INSERT INTO " . $this->items_table . "
                        SET
                            order_id = :order_id,
                            product_id = :product_id,
                            quantity = :quantity,
                            price = :price";
                
                // Prepare query
                $stmt = $this->conn->prepare($query);
                
                // Sanitize
                $order_id = htmlspecialchars(strip_tags($this->id));
                $product_id = htmlspecialchars(strip_tags($item['product_id']));
                $quantity = htmlspecialchars(strip_tags($item['quantity']));
                $price = htmlspecialchars(strip_tags($item['price']));
                
                // Bind values
                $stmt->bindParam(":order_id", $order_id);
                $stmt->bindParam(":product_id", $product_id);
                $stmt->bindParam(":quantity", $quantity);
                $stmt->bindParam(":price", $price);
                
                // Execute query
                $stmt->execute();
            }
            
            // Commit transaction
            $this->conn->commit();
            
            return true;
        } catch(Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            
            return false;
        }
    }

    // Get orders by user
    public function getByUser() {
        // Query to get orders
        $query = "SELECT id, total_amount, status, created
                FROM " . $this->table_name . "
                WHERE user_id = ?
                ORDER BY created DESC";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind value
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }

    // Get order details
    public function getDetails() {
        // Query to get order
        $query = "SELECT id, user_id, total_amount, status, created
                FROM " . $this->table_name . "
                WHERE id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind value
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->user_id = $row['user_id'];
            $this->total_amount = $row['total_amount'];
            $this->status = $row['status'];
            $this->created = $row['created'];
            
            // Get order items
            $query = "SELECT oi.product_id, oi.quantity, oi.price, p.name, p.image
                    FROM " . $this->items_table . " oi
                    LEFT JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?";
            
            // Prepare query
            $stmt = $this->conn->prepare($query);
            
            // Bind value
            $stmt->bindParam(1, $this->id);
            
            // Execute query
            $stmt->execute();
            
            // Store items in array
            $this->items = array();
            while($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($this->items, $item);
            }
            
            return true;
        }
        
        return false;
    }

    // Get orders by seller
    public function getBySeller() {
        // Query to get orders containing seller's products
        $query = "SELECT DISTINCT o.id, o.user_id, o.total_amount, o.status, o.created
                FROM " . $this->table_name . " o
                JOIN " . $this->items_table . " oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE p.seller_id = ?
                ORDER BY o.created DESC";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind value
        $stmt->bindParam(1, $this->user_id); // Using user_id to store seller_id
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }

    // Update order status
    public function updateStatus() {
        // Query to update order status
        $query = "UPDATE " . $this->table_name . "
                SET status = :status
                WHERE id = :id";
        
        // Prepare query
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
}
?>

