<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

// Set user ID
$order->user_id = $_SESSION['user_id'];

// Get orders based on role
if($_SESSION['role'] === 'customer'){
    $stmt = $order->getByUser();
}
else if($_SESSION['role'] === 'seller'){
    $stmt = $order->getBySeller();
}
else{
    // Admin can see all orders, but we'll implement that separately
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Invalid role."));
    exit;
}

$num = $stmt->rowCount();

// Check if more than 0 record found
if($num > 0){
    // Orders array
    $orders_arr = array();
    $orders_arr["records"] = array();
    
    // Retrieve table contents
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        
        $order_item = array(
            "id" => $id,
            "total_amount" => $total_amount,
            "status" => $status,
            "created" => $created
        );
        
        array_push($orders_arr["records"], $order_item);
    }
    
    // Set response code - 200 OK
    http_response_code(200);
    
    // Show orders data
    echo json_encode($orders_arr);
}
else{
    // Set response code - 200 OK
    http_response_code(200);
    
    // Tell the user no orders found
    echo json_encode(array("message" => "No orders found.", "records" => array()));
}
?>

