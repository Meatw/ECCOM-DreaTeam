<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and cart model
include_once '../../config/database.php';
include_once '../../models/Cart.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate cart object
$cart = new Cart($db);

// Check if user is logged in and is a customer
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer'){
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Only customers can remove items from cart."));
    exit;
}

// Get cart item id
$data = json_decode(file_get_contents("php://input"));

// Make sure cart item id is not empty
if(!empty($data->id)){
    // Set cart item id to be removed
    $cart->id = $data->id;
    $cart->user_id = $_SESSION['user_id']; // Ensure user can only remove their own cart items
    
    // Remove item from cart
    if($cart->removeItem()){
        // Set response code - 200 OK
        http_response_code(200);
        
        // Tell the user
        echo json_encode(array("message" => "Item removed from cart."));
    }
    else{
        // Set response code - 503 service unavailable
        http_response_code(503);
        
        // Tell the user
        echo json_encode(array("message" => "Unable to remove item from cart."));
    }
}
else{
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to remove item from cart. Cart item ID is required."));
}
?>

