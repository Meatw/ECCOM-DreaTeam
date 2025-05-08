<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and review model
include_once '../../config/database.php';
include_once '../../models/Review.php';
include_once '../../models/Product.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate objects
$review = new Review($db);
$product = new Product($db);

// Check if user is logged in and is a seller
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller'){
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Only sellers can reply to reviews."));
    exit;
}

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if(
    !empty($data->review_id) &&
    !empty($data->product_id) &&
    !empty($data->reply)
){
    // Check if product belongs to seller
    $product->id = $data->product_id;
    $product->seller_id = $_SESSION['user_id'];
    
    if($product->readOne()){
        // Set review property values
        $review->id = $data->review_id;
        $review->seller_reply = $data->reply;
        
        // Add reply to review
        if($review->addReply()){
            // Set response code - 200 OK
            http_response_code(200);
            
            // Tell the user
            echo json_encode(array("message" => "Reply added to review."));
        }
        else{
            // Set response code - 503 service unavailable
            http_response_code(503);
            
            // Tell the user
            echo json_encode(array("message" => "Unable to add reply to review."));
        }
    }
    else{
        // Set response code - 403 Forbidden
        http_response_code(403);
        
        // Tell the user
        echo json_encode(array("message" => "Access denied. You can only reply to reviews of your own products."));
    }
}
else{
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to add reply to review. Data is incomplete."));
}
?>

