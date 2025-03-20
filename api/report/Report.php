<?php
class Report {
    // Database connection and table names
    private $conn;
    private $reported_items_table = "reported_items";
    private $reported_businesses_table = "reported_businesses";
    
    // Object properties
    public $id;
    public $user_id;
    public $product_id;
    public $business_id;
    public $reason;
    public $status;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Report product
    public function reportProduct() {
        // Check if user has already reported this product
        $check_query = "SELECT id FROM " . $this->reported_items_table . " WHERE user_id = ? AND product_id = ?";
        
        // Prepare query
        $check_stmt = $this->conn->prepare($check_query);
        
        // Bind values
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->bindParam(2, $this->product_id);
        
        // Execute query
        $check_stmt->execute();
        
        // If report exists, return false (already reported)
        if ($check_stmt->rowCount() > 0) {
            return false;
        }
        
        // If report doesn't exist, create new one
        $query = "INSERT INTO " . $this->reported_items_table . "
                SET
                    user_id=:user_id,
                    product_id=:product_id,
                    reason=:reason,
                    status='pending'";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->reason = htmlspecialchars(strip_tags($this->reason));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":reason", $this->reason);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Report business
    public function reportBusiness() {
        // Check if user has already reported this business
        $check_query = "SELECT id FROM " . $this->reported_businesses_table . " WHERE user_id = ? AND business_id = ?";
        
        // Prepare query
        $check_stmt = $this->conn->prepare($check_query);
        
        // Bind values
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->bindParam(2, $this->business_id);
        
        // Execute query
        $check_stmt->execute();
        
        // If report exists, return false (already reported)
        if ($check_stmt->rowCount() > 0) {
            return false;
        }
        
        // If report doesn't exist, create new one
        $query = "INSERT INTO " . $this->reported_businesses_table . "
                SET
                    user_id=:user_id,
                    business_id=:business_id,
                    reason=:reason,
                    status='pending'";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->reason = htmlspecialchars(strip_tags($this->reason));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":reason", $this->reason);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Get reported products (for admin)
    public function getReportedProducts($limit = 10, $offset = 0, $status = null) {
        // Query to get reported products
        $query = "SELECT ri.*, p.name as product_name, p.image as product_image,
                  u.name as reporter_name, b.name as business_name
                  FROM " . $this->reported_items_table . " ri
                  JOIN products p ON ri.product_id = p.id
                  JOIN users u ON ri.user_id = u.id
                  JOIN businesses b ON p.business_id = b.id
                  WHERE 1=1";
        
        // Add status filter if provided
        if ($status) {
            $query .= " AND ri.status = :status";
        }
        
        $query .= " ORDER BY ri.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind status if provided
        if ($status) {
            $stmt->bindParam(":status", $status);
        }
        
        // Bind limit and offset
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get reported businesses (for admin)
    public function getReportedBusinesses($limit = 10, $offset = 0, $status = null) {
        // Query to get reported businesses
        $query = "SELECT rb.*, b.name as business_name, b.logo as business_logo,
                  u.name as reporter_name, bu.name as business_owner_name
                  FROM " . $this->reported_businesses_table . " rb
                  JOIN businesses b ON rb.business_id = b.id
                  JOIN users u ON rb.user_id = u.id
                  JOIN users bu ON b.user_id = bu.id
                  WHERE 1=1";
        
        // Add status filter if provided
        if ($status) {
            $query .= " AND rb.status = :status";
        }
        
        $query .= " ORDER BY rb.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind status if provided
        if ($status) {
            $stmt->bindParam(":status", $status);
        }
        
        // Bind limit and offset
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Update report status (for admin)
    public function updateReportStatus($report_id, $report_type, $status) {
        // Determine table based on report type
        $table = ($report_type === 'product') ? $this->reported_items_table : $this->reported_businesses_table;
        
        // Query to update status
        $query = "UPDATE " . $table . " SET status = ? WHERE id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $status = htmlspecialchars(strip_tags($status));
        $report_id = htmlspecialchars(strip_tags($report_id));
        
        // Bind values
        $stmt->bindParam(1, $status);
        $stmt->bindParam(2, $report_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Remove reported product (for admin)
    public function removeReportedProduct($product_id) {
        // Update product status to 'deleted'
        $query = "UPDATE products SET status = 'deleted' WHERE id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $product_id = htmlspecialchars(strip_tags($product_id));
        
        // Bind value
        $stmt->bindParam(1, $product_id);
        
        // Execute query
        if ($stmt->execute()) {
            // Update all reports for this product to 'resolved'
            $update_query = "UPDATE " . $this->reported_items_table . " SET status = 'resolved' WHERE product_id = ?";
            
            // Prepare query
            $update_stmt = $this->conn->prepare($update_query);
            
            // Bind value
            $update_stmt->bindParam(1, $product_id);
            
            // Execute query
            $update_stmt->execute();
            
            return true;
        }
        
        return false;
    }
}
?>