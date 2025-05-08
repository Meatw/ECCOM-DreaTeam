<?php
class Review {
    // Database connection and table name
    private $conn;
    private $table_name = "reviews";

    // Object properties
    public $id;
    public $product_id;
    public $user_id;
    public $rating;
    public $comment;
    public $created;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create review
    public function create() {
        // Query to insert record
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    product_id = :product_id,
                    user_id = :user_id,
                    rating = :rating,
                    comment = :comment,
                    created = NOW()";
    
        // Prepare query
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->product_id = htmlspecialchars(strip_tags($this->product_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->rating = htmlspecialchars(strip_tags($this->rating));
        $this->comment = htmlspecialchars(strip_tags($this->comment));
    
        // Bind values
        $stmt->bindParam(":product_id", $this->product_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":comment", $this->comment);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }

    // Read reviews by product
    public function readByProduct() {
        // Query to select reviews by product
        $query = "SELECT
                    r.id, r.rating, r.comment, r.created,
                    u.username
                FROM
                    " . $this->table_name . " r
                    LEFT JOIN
                        users u ON r.user_id = u.id
                WHERE
                    r.product_id = ?
                ORDER BY
                    r.created DESC";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Bind product ID
        $stmt->bindParam(1, $this->product_id);
    
        // Execute query
        $stmt->execute();
    
        return $stmt;
    }

    // Seller: Reply to review
    public function addReply() {
        // Query to update review with seller reply
        $query = "UPDATE " . $this->table_name . "
                SET
                    seller_reply = :seller_reply,
                    reply_date = NOW()
                WHERE
                    id = :id";
    
        // Prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->seller_reply = htmlspecialchars(strip_tags($this->seller_reply));
        $this->id = htmlspecialchars(strip_tags($this->id));
    
        // Bind values
        $stmt->bindParam(":seller_reply", $this->seller_reply);
        $stmt->bindParam(":id", $this->id);
    
        // Execute query
        if($stmt->execute()) {
            return true;
        }
    
        return false;
    }
}
?>

