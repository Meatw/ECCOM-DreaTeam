<?php
class Review {
    // Database connection and table names
    private $conn;
    private $product_reviews_table = "product_reviews";
    private $business_reviews_table = "business_reviews";
    private $review_comments_table = "review_comments";
    
    // Object properties
    public $id;
    public $user_id;
    public $product_id;
    public $business_id;
    public $rating;
    public $comment;
    public $created_at;
    public $updated_at;
    public $review_type; // 'product' or 'business'
    
    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Create product review
    public function createProductReview() {
        // Check if user has already reviewed this product
        $check_query = "SELECT id FROM " . $this->product_reviews_table . " WHERE user_id = ? AND product_id = ?";
        
        // Prepare query
        $check_stmt = $this->conn->prepare($check_query);
        
        // Bind values
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->bindParam(2, $this->product_id);
        
        // Execute query
        $check_stmt->execute();
        
        // If review exists, update it
        if ($check_stmt->rowCount() > 0) {
            $row = $check_stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            
            // Update query
            $query = "UPDATE " . $this->product_reviews_table . "
                    SET
                        rating=:rating,
                        comment=:comment
                    WHERE id=:id";
            
            // Prepare query
            $stmt = $this->conn->prepare($query);
            
            // Sanitize
            $this->rating = htmlspecialchars(strip_tags($this->rating));
            $this->comment = htmlspecialchars(strip_tags($this->comment));
            $this->id = htmlspecialchars(strip_tags($this->id));
            
            // Bind values
            $stmt->bindParam(":rating", $this->rating);
            $stmt->bindParam(":comment", $this->comment);
            $stmt->bindParam(":id", $this->id);
            
            // Execute query
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
        }
        
        // If review doesn't exist, create new one
        $query = "INSERT INTO " . $this->product_reviews_table . "
                SET
                    user_id=:user_id,
                    product_id=:product_id,
                    rating=:rating,
                    comment=:comment";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->comment = htmlspecialchars(strip_tags($this->comment));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":comment", $this->comment);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Create business review
    public function createBusinessReview() {
        // Check if user has already reviewed this business
        $check_query = "SELECT id FROM " . $this->business_reviews_table . " WHERE user_id = ? AND business_id = ?";
        
        // Prepare query
        $check_stmt = $this->conn->prepare($check_query);
        
        // Bind values
        $check_stmt->bindParam(1, $this->user_id);
        $check_stmt->bindParam(2, $this->business_id);
        
        // Execute query
        $check_stmt->execute();
        
        // If review exists, update it
        if ($check_stmt->rowCount() > 0) {
            $row = $check_stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            
            // Update query
            $query = "UPDATE " . $this->business_reviews_table . "
                    SET
                        rating=:rating,
                        comment=:comment
                    WHERE id=:id";
            
            // Prepare query
            $stmt = $this->conn->prepare($query);
            
            // Sanitize
            $this->rating = htmlspecialchars(strip_tags($this->rating));
            $this->comment = htmlspecialchars(strip_tags($this->comment));
            $this->id = htmlspecialchars(strip_tags($this->id));
            
            // Bind values
            $stmt->bindParam(":rating", $this->rating);
            $stmt->bindParam(":comment", $this->comment);
            $stmt->bindParam(":id", $this->id);
            
            // Execute query
            if ($stmt->execute()) {
                return true;
            }
            
            return false;
        }
        
        // If review doesn't exist, create new one
        $query = "INSERT INTO " . $this->business_reviews_table . "
                SET
                    user_id=:user_id,
                    business_id=:business_id,
                    rating=:rating,
                    comment=:comment";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->business_id = htmlspecialchars(strip_tags($this->business_id));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->comment = htmlspecialchars(strip_tags($this->comment));
        
        // Bind values
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":business_id", $this->business_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":comment", $this->comment);
        
        // Execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    // Add comment to review
    public function addComment($review_id, $review_type, $user_id, $comment) {
        // Insert comment
        $query = "INSERT INTO " . $this->review_comments_table . "
                SET
                    review_id=:review_id,
                    review_type=:review_type,
                    user_id=:user_id,
                    comment=:comment";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $review_id = htmlspecialchars(strip_tags($review_id));
        $review_type = htmlspecialchars(strip_tags($review_type));
        $user_id = htmlspecialchars(strip_tags($user_id));
        $comment = htmlspecialchars(strip_tags($comment));
        
        // Bind values
        $stmt->bindParam(":review_id", $review_id);
        $stmt->bindParam(":review_type", $review_type);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":comment", $comment);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    // Get product reviews
    public function getProductReviews($limit = 10, $offset = 0) {
        // Query to get product reviews
        $query = "SELECT pr.*, u.name as user_name, u.profile_image as user_image
                  FROM " . $this->product_reviews_table . " pr
                  JOIN users u ON pr.user_id = u.id
                  WHERE pr.product_id = ?
                  ORDER BY pr.created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind product ID
        $stmt->bindParam(1, $this->product_id);
        
        // Bind limit and offset
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
    
    // Get business reviews
    public function getBusinessReviews($limit = 10, $offset = 0) {
        // Query to get business reviews
        $query = "SELECT br.*, u.name as user_name, u.profile_image as user_image
                  FROM " . $this->business_reviews_table . " br
                  JOIN users u ON br.user_id = u.id
                  WHERE br.business_id = ?
                  ORDER BY br.created_at DESC
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
    
    // Get review comments
    public function getReviewComments($review_id, $review_type) {
        // Query to get review comments
        $query = "SELECT rc.*, u.name as user_name, u.profile_image as user_image, u.user_type
                  FROM " . $this->review_comments_table . " rc
                  JOIN users u ON rc.user_id = u.id
                  WHERE rc.review_id = ? AND rc.review_type = ?
                  ORDER BY rc.created_at ASC";
        
        // Prepare query
        $stmt = $this->conn->prepare($query);
        
        // Bind review ID and type
        $stmt->bindParam(1, $review_id);
        $stmt->bindParam(2, $review_type);
        
        // Execute query
        $stmt->execute();
        
        return $stmt;
    }
}
?>