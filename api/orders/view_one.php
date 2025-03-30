<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Include database and order model
include_once '../../config/database.php';
include_once '../../models/Order.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate order object
$order = new Order($db);

// Check if user is logged in
session_start();
if(!isset($_SESSION['user_id'])){
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Please login."));
    exit;
}

// Set ID property of record to read
$order->id = isset($_GET['id']) ? $_GET['id'] : die();

// Read the details of order
if($order->getDetails()){
    // Check if user has permission to view this order
    if($_SESSION['role'] === 'customer' && $order->user_id != $_SESSION['user_id']){
        // Set response code - 403 Forbidden
        http_response_code(403);
        
        // Tell the user
        echo json_encode(array("message" => "Access denied. You can only view your own orders."));
        exit;
    }
    
    // Create array
    $order_arr = array(
        "id" => $order->id,
        "user_id" => $order->user_id,
        "total_amount" => $order->total_amount,
        "status" => $order->status,
        "created" => $order->created,
        "items" => $order->items
    );
    
    // Set response code - 200 OK
    http_response_code(200);
    
    // Make it json format
    echo json_encode($order_arr);
}
else{
    // Set response code - 404 Not found
    http_response_code(404);
    
    // Tell the user order does not exist
    echo json_encode(array("message" => "Order does not exist."));
}
?>

