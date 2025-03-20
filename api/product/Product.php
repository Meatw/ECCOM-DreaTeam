<?php
class Product {
    // Database connection and table name
    private $conn;
    private $table_name = "products";
    
    // Object properties
    public $id;
    public $business_id;
    public $category_id;
    public $name;
    public $description;
    public $price;
    public $stock_quantity;
    public $image;
    public $status;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create product
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    business_id=:business_id,
                    category_id=:category_id,
                    name=:name,
                    description=:description,
                    price=:price,
                    stock_quantity=:stock_quantity,
                    status=:status";
        
        // Add image to query if it exists
        if (!empty($this->image)) {
            $query .= ", image=:image";
        }
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->status = htmlspecialchars(strip_tags($this->status));
        
        // Bind values
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":status", $this->status);
        
        // Bind image if it exists
        if (!empty($this->image)) {
            $this->image = htmlspecialchars(strip_tags($this->image));
            $stmt->bindParam(":image", $this->image);
        }
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Get product by ID
    public function getById() {
        // Query to read single record
        $query = "SELECT p.*, c.name as category_name, b.name as business_name, b.user_id as business_owner_id
                  FROM " . $this->table_name . " p
                  JOIN categories c ON p.category_id = c.id
                  JOIN businesses b ON p.business_id = b.id
                  WHERE p.id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind ID
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        $stmt->execute();
        
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Set values to object properties
            $this->business_id = $row['business_id'];
            $this->category_id = $row['category_id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->stock_quantity = $row['stock_quantity'];
            $this->image = $row['image'];
            $this->status = $row['status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            // Additional properties
            $this->category_name = $row['category_name'];
            $this->business_name = $row['business_name'];
            $this->business_owner_id = $row['business_owner_id'];
            
            return true;
        }
        
        return false;
    }
    
    // Update product
    public function update() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    category_id=:category_id,
                    name=:name,
                    description=:description,
                    price=:price,
                    stock_quantity=:stock_quantity,
                    status=:status";
        
        // Add image to query if it exists
        if (!empty($this->image)) {
            $query .= ", image=:image";
        }
        
        $query .= " WHERE id=:id AND business_id=:business_id";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->stock_quantity = htmlspecialchars(strip_tags($this->stock_quantity));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        
        // Bind values
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":stock_quantity", $this->stock_quantity);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":business_id", $this->business_id);
        
        // Bind image if it exists
        if (!empty($this->image)) {
            $this->image = htmlspecialchars(strip_tags($this->image));
            $stmt->bindParam(":image", $this->image);
        }
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete product
    public function delete() {
        // Query to delete record
        $query = "UPDATE " . $this->table_name . " SET status = 'deleted' WHERE id = ? AND business_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        
        // Bind IDs
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->business_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get all products
    public function getAll($limit = 10, $offset = 0, $category_id = null, $business_id = null, $filter = null, $search = null) {
        // Query to select all products
        $query = "SELECT p.*, c.name as category_name, b.name as business_name,
                  (SELECT AVG(rating) FROM product_reviews WHERE product_id = p.id) as avg_rating,
                  (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id) as review_count
                  FROM " . $this->table_name . " p
                  JOIN categories c ON p.category_id = c.id
                  JOIN businesses b ON p.business_id = b.id
                  WHERE p.status != 'deleted' AND b.is_approved = 1";
        
        // Add category filter if provided
        if ($category_id) {
            $query .= " AND p.category_id = :category_id";
        }
        
        // Add business filter if provided
        if ($business_id) {
            $query .= " AND p.business_id = :business_id";
        }
        
        // Add search condition if provided
        if ($search) {
            $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
        }
        
        // Add filter conditions
        if ($filter) {
            switch ($filter) {
                case 'new':
                    $query .= " ORDER BY p.created_at DESC";
                    break;
                case 'top_rated':
                    $query .= " ORDER BY avg_rating DESC, review_count DESC";
                    break;
                case 'price_low':
                    $query .= " ORDER BY p.price ASC";
                    break;
                case 'price_high':
                    $query .= " ORDER BY p.price DESC";
                    break;
                default:
                    $query .= " ORDER BY p.created_at DESC";
            }
        } else {
            $query .= " ORDER BY p.created_at DESC";
        }
        
        $query .= " LIMIT :limit OFFSET :offset";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind category_id if provided
        if ($category_id) {
            $stmt->bindParam(":category_id", $category_id);
        }
        
        // Bind business_id if provided
        if ($business_id) {
            $stmt->bindParam(":business_id", $business_id);
        }
        
        // Bind search if provided
        if ($search) {
            $searchParam = "%" . $search . "%";
            $stmt->bindParam(":search", $searchParam);
        }
        
        // Bind limit and offset
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get products by business ID
    public function getByBusinessId($limit = 10, $offset = 0) {
        // Query to select products by business ID
        $query = "SELECT p.*, c.name as category_name,
                  (SELECT AVG(rating) FROM product_reviews WHERE product_id = p.id) as avg_rating,
                  (SELECT COUNT(*) FROM product_reviews WHERE product_id = p.id) as review_count
                  FROM " . $this->table_name . " p
                  JOIN categories c ON p.category_id = c.id
                  WHERE p.business_id = ? AND p.status != 'deleted'
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind business ID
        $stmt->bindParam(1, $this->business_id);
        
        // Bind limit and offset
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get top selling products for a business
    public function getTopSelling($limit = 5) {
        // Query to select top selling products
        $query = "SELECT p.*, c.name as category_name,
                  (SELECT COUNT(*) FROM order_items WHERE product_id = p.id) as order_count,
                  (SELECT SUM(quantity) FROM order_items WHERE product_id = p.id) as total_quantity
                  FROM " . $this->table_name . " p
                  JOIN categories c ON p.category_id = c.id
                  WHERE p.business_id = ? AND p.status != 'deleted'
                  ORDER BY total_quantity DESC, order_count DESC
                  LIMIT ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind business ID and limit
        $stmt->bindParam(1, $this->business_id);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
}
?>