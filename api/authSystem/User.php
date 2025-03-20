<?php
class User {
    // Database connection and table name
    private $conn;
    private $table_name = "users";
    
    // Object properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $user_type;
    public $profile_image;
    public $phone;
    public $address;
    public $created_at;
    public $updated_at;
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Register user
    public function register() {
        // Check if email already exists
        if ($this->emailExists()) {
            return false;
        }
        
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name=:name,
                    email=:email,
                    password=:password,
                    user_type=:user_type,
                    phone=:phone,
                    address=:address";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->user_type = htmlspecialchars(strip_tags($this->user_type));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        
        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);
        
        $stmt->bindParam(":user_type", $this->user_type);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Login user
    public function login() {
        // Query to check if email exists
        $query = "SELECT id, name, email, password, user_type FROM " . $this->table_name . " WHERE email = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind value
        $stmt->bindParam(1, $this->email);
        
        // Execute query
        $stmt->execute();
        
        // Get row count
        $num = $stmt->rowCount();
        
        // If email exists, check password
        if ($num > 0) {
            // Get record details
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (password_verify($this->password, $row['password'])) {
                // Set values to object properties
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->user_type = $row['user_type'];
                
                return true;
            }
        }
        
        return false;
    }
    
    // Check if email exists
    public function emailExists() {
        // Query to check if email exists
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind value
        $stmt->bindParam(1, $this->email);
        
        // Execute query
        $stmt->execute();
        
        // Get row count
        $num = $stmt->rowCount();
        
        // If email exists
        if ($num > 0) {
            return true;
        }
        
        return false;
    }
    
    // Get user by ID
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
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->user_type = $row['user_type'];
            $this->profile_image = $row['profile_image'];
            $this->phone = $row['phone'];
            $this->address = $row['address'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            return true;
        }
        
        return false;
    }
    
    // Update user profile
    public function update() {
        // Query to update record
        $query = "UPDATE " . $this->table_name . "
                SET
                    name=:name,
                    phone=:phone,
                    address=:address";
        
        // Add profile image to query if it exists
        if (!empty($this->profile_image)) {
            $query .= ", profile_image=:profile_image";
        }
        
        // Add password to query if it exists
        if (!empty($this->password)) {
            $query .= ", password=:password";
        }
        
        $query .= " WHERE id=:id";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":address", $this->address);
        $stmt->bindParam(":id", $this->id);
        
        // Bind profile image if it exists
        if (!empty($this->profile_image)) {
            $this->profile_image = htmlspecialchars(strip_tags($this->profile_image));
            $stmt->bindParam(":profile_image", $this->profile_image);
        }
        
        // Bind password if it exists
        if (!empty($this->password)) {
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
        }
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Delete user account
    public function delete() {
        // Query to delete record
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Bind id
        $stmt->bindParam(1, $this->id);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get all users (for admin)
    public function getAll($limit = 10, $offset = 0, $search = "") {
        // Query to select all users
        $query = "SELECT id, name, email, user_type, created_at FROM " . $this->table_name;
        
        // Add search condition if search parameter is provided
        if (!empty($search)) {
            $query .= " WHERE name LIKE :search OR email LIKE :search";
        }
        
        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
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
    
    // Create admin account (for admin)
    public function createAdmin($isLimited = true) {
        // Check if email already exists
        if ($this->emailExists()) {
            return false;
        }
        
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    name=:name,
                    email=:email,
                    password=:password,
                    user_type=:user_type";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Set user type based on isLimited parameter
        $userType = $isLimited ? "limited_admin" : "admin";
        
        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        
        // Hash the password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);
        
        $stmt->bindParam(":user_type", $userType);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
}
?>