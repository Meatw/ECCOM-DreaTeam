<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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
    echo json_encode(array("message" => "Access denied. Only customers can add items to cart."));
    exit;
}

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if(
    !empty($data->product_id) &&
    !empty($data->quantity)
){
    // Set cart property values
    $cart->user_id = $_SESSION['user_id'];
    $cart->product_id = $data->product_id;
    $cart->quantity = $data->quantity;
    
    // Add item to cart
    if($cart->addItem()){
        // Set response code - 201 created
        http_response_code(201);
        
        // Tell the user
        echo json_encode(array("message" => "Item added to cart."));
    }
    else{
        // Set response code - 503 service unavailable
        http_response_code(503);
        
        // Tell the user
        echo json_encode(array("message" => "Unable to add item to cart."));
    }
}
else{
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to add item to cart. Data is incomplete."));
}
?>

