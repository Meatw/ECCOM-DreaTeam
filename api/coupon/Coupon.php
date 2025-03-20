<?php
class Coupon {
    // Database connection and table name
    private $conn;
    private $table_name = "coupons";
    
    // Object properties
    public $id;
    public $business_id;
    public $code;
    public $discount_type;
    public $discount_value;
    public $min_purchase;
    public $product_id;
    public $start_date;
    public $end_date;
    public $is_active;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create coupon
    public function create() {
        // Check if code already exists
        $check_query = "SELECT id FROM " . $this->table_name . " WHERE code = ?";
        
        // Prepare query
        $check_stmt = $this->conn->prepare($check_query);
        
        // Bind value
        $check_stmt->bindParam(1, $this->code);
        
        // Execute query
        $check_stmt->execute();
        
        // If code exists, return false
        if ($check_stmt->rowCount() > 0) {
            return false;
        }
        
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    business_id=:business_id,
                    code=:code,
                    discount_type=:discount_type,
                    discount_value=:discount_value,
                    min_purchase=:min_purchase,
                    start_date=:start_date,
                    end_date=:end_date,
                    is_active=:is_active";
        
        // Add product_id to query if it exists
        if (!empty($this->product_id)) {
            $query .= ", product_id=:product_id";
        }
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->code = htmlspecialchars(strip_tags($this->code));
        $this->discount_type = htmlspecialchars(strip_tags($this->discount_type));
        $this->discount_value = htmlspecialchars(strip_tags($this->discount_value));
        $this->min_purchase = htmlspecialchars(strip_tags($this->min_purchase));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        
        // Set is_active to true by default
        $isActive = 1;
        
        // Bind values
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":code", $this->code);
        $stmt->bindParam(":discount_type", $this->discount_type);
        $stmt->bindParam(":discount_value", $this->discount_value);
        $stmt->bindParam(":min_purchase", $this->min_purchase);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":is_active", $isActive);
        
        // Bind product_id if it exists
        if (!empty($this->product_id)) {
            $this->product_id = htmlspecialchars(strip_tags($this->product_id));
            $stmt->bindParam(":product_id", $this->product_id);
        }
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Get coupon by code
    public function getByCode() {
        // Query to read single record
        $query = "SELECT c.*, b.name as business_name, p.name as product_name
                  FROM " . $this->table_name . " c
                  JOIN businesses b ON c.business_id = b.id
                  LEFT JOIN products p ON c.product_id = p.id
                  WHERE c.code = ? AND c.is_active = 1
                  AND c.start_date <= NOW() AND c.end_date >= NOW()";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind code
        $stmt->bindParam(1, $this->code);
        
        // Execute query
        $stmt->execute();
        
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Set values to object properties
            $this->id = $row['id'];
            $this->business_id = $row['business_id'];
            $this->discount_type = $row['discount_type'];
            $this->discount_value = $row['discount_value'];
            $this->min_purchase = $row['min_purchase'];
            $this->product_id = $row['product_id'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            // Additional properties
            $this->business_name = $row['business_name'];
            $this->product_name = $row['product_name'];
            
            return true;
        }
        
        return false;
    }
    
    // Get business coupons
    public function getBusinessCoupons() {
        // Query to get business coupons
        $query = "SELECT c.*, p.name as product_name
                  FROM " . $this->table_name . " c
                  LEFT JOIN products p ON c.product_id = p.id
                  WHERE c.business_id = ?
                  ORDER BY c.created_at DESC";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind business ID
        $stmt->bindParam(1, $this->business_id);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Update coupon status
    public function updateStatus() {
        // Query to update status
        $query = "UPDATE " . $this->table_name . " SET is_active = ? WHERE id = ? AND business_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        
        // Bind values
        $stmt->bindParam(1, $this->is_active);
        $stmt->bindParam(2, $this->id);
        $stmt->bindParam(3, $this->business_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete coupon
    public function delete() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ? AND business_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        
        // Bind values
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->business_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Apply coupon to cart
    public function applyToCart($cart_items, $total_amount) {
        // Check if coupon is valid
        if (!$this->getByCode()) {
            return false;
        }
        
        // Check minimum purchase requirement
        if ($total_amount < $this->min_purchase) {
            return false;
        }
        
        // Calculate discount
        $discount = 0;
        
        // If coupon is for a specific product
        if (!empty($this->product_id)) {
            // Find the product in cart
            $product_total = 0;
            foreach ($cart_items as $item) {
                if ($item['product_id'] == $this->product_id) {
                    $product_total += ($item['price'] * $item['quantity']);
                }
            }
            
            // If product not in cart or quantity is 0, return false
            if ($product_total <= 0) {
                return false;
            }
            
            // Calculate discount based on product total
            if ($this->discount_type == 'percentage') {
                $discount = $product_total * ($this->discount_value / 100);
            } else { // fixed amount
                $discount = min($this->discount_value, $product_total);
            }
        } else {
            // Coupon applies to entire cart
            if ($this->discount_type == 'percentage') {
                $discount = $total_amount * ($this->discount_value / 100);
            } else { // fixed amount
                $discount = min($this->discount_value, $total_amount);
            }
        }
        
        return $discount;
    }
}
?>