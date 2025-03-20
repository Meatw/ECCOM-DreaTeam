<?php
class Business {
    // Database connection and table name
    private $conn;
    private $table_name = "businesses";
    
    // Object properties
    public $id;
    public $user_id;
    public $name;
    public $description;
    public $logo;
    public $banner;
    public $address;
    public $phone;
    public $email;
    public $website;
    public $is_approved;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create business
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    user_id=:user_id,
                    name=:name,
                    description=:description,
                    address=:address,
                    phone=:phone,
                    email=:email,
                    website=:website,
                    is_approved=:is_approved";
        
        // Add logo to query if it exists
        if (!empty($this->logo)) {
            $query .= ", logo=:logo";
        }
        
        // Add banner to query if it exists
        if (!empty($this->banner)) {
            $query .= ", banner=:banner";
        }
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->website = htmlspecialchars(strip_tags($this->website));
        
        // Set is_approved to false by default
        $isApproved = 0;
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":website", $this->website);
        $stmt->bindParam(":is_approved", $isApproved);
        
        // Bind logo if it exists
        if (!empty($this->logo)) {
            $this->logo = htmlspecialchars(strip_tags($this->logo));
            $stmt->bindParam(":logo", $this->logo);
        }
        
        // Bind banner if it exists
        if (!empty($this->banner)) {
            $this->banner = htmlspecialchars(strip_tags($this->banner));
            $stmt->bindParam(":banner", $this->banner);
        }
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Get business by ID
    public function getById() {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        
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
            $this->user_id = $row['user_id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->logo = $row['logo'];
            $this->banner = $row['banner'];
            $this->address = $row['address'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->website = $row['website'];
            $this->is_approved = $row['is_approved'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Get business by user ID
    public function getByUserId() {
        // Query to read single record
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind user ID
        $stmt->bindParam(1, $this->user_id);
        
        // Execute query
        $stmt->execute();
        
        // Get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Set values to object properties
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->logo = $row['logo'];
            $this->banner = $row['banner'];
            $this->address = $row['address'];
            $this->phone = $row['phone'];
            $this->email = $row['email'];
            $this->website = $row['website'];
            $this->is_approved = $row['is_approved'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Update business profile
    public function update() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    name=:name,
                    description=:description,
                    address=:address,
                    phone=:phone,
                    email=:email,
                    website=:website";
        
        // Add logo to query if it exists
        if (!empty($this->logo)) {
            $query .= ", logo=:logo";
        }
        
        // Add banner to query if it exists
        if (!empty($this->banner)) {
            $query .= ", banner=:banner";
        }
        
        $query .= " WHERE id=:id AND user_id=:user_id";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->website = htmlspecialchars(strip_tags($this->website));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":website", $this->website);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);
        
        // Bind logo if it exists
        if (!empty($this->logo)) {
            $this->logo = htmlspecialchars(strip_tags($this->logo));
            $stmt->bindParam(":logo", $this->logo);
        }
        
        // Bind banner if it exists
        if (!empty($this->banner)) {
            $this->banner = htmlspecialchars(strip_tags($this->banner));
            $stmt->bindParam(":banner", $this->banner);
        }
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete business
    public function delete() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ? AND user_id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind IDs
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->user_id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Approve business (for admin)
    public function approve() {
        // Query to update approval status
        $query = "UPDATE " . $this->table_name . " SET is_approved = 1 WHERE id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind ID
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get all businesses
    public function getAll($limit = 10, $offset = 0, $approved_only = true, $search = "") {
        // Query to select all businesses
        $query = "SELECT b.*, u.name as owner_name FROM " . $this->table_name . " b
                  JOIN users u ON b.user_id = u.id
                  WHERE 1=1";
        
        // Add approval condition if approved_only is true
        if ($approved_only) {
            $query .= " AND b.is_approved = 1";
        }
        
        // Add search condition if search parameter is provided
        if (!empty($search)) {
            $query .= " AND (b.name LIKE :search OR b.description LIKE :search)";
        }
        
        $query .= " ORDER BY b.created_at DESC LIMIT :limit OFFSET :offset";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind search parameter if provided
        if (!empty($search)) {
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
    
    // Get pending approval businesses (for admin)
    public function getPendingApproval($limit = 10, $offset = 0) {
        // Query to select pending approval businesses
        $query = "SELECT b.*, u.name as owner_name FROM " . $this->table_name . " b
                  JOIN users u ON b.user_id = u.id
                  WHERE b.is_approved = 0
                  ORDER BY b.created_at ASC LIMIT :limit OFFSET :offset";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind limit and offset
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
}
?>