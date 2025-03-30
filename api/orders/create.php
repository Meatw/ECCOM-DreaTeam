<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and models
include_once '../../config/database.php';
include_once '../../models/Order.php';
include_once '../../models/Cart.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate objects
$order = new Order($db);
$cart = new Cart($db);

// Check if user is logged in and is a customer
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer'){
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Only customers can create orders."));
    exit;
}

// Set user ID
$cart->user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $cart->getItems();
$num = $stmt->rowCount();

// Check if cart is not empty
if($num > 0){
    // Set order properties
    $order->user_id = $_SESSION['user_id'];
    $order->total_amount = 0;
    $order->items = array();
    
    // Calculate total and prepare order items
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        
        $subtotal = $price * $quantity;
        $order->total_amount += $subtotal;
        
        $item = array(
            "product_id" => $product_id,
            "quantity" => $quantity,
            "price" => $price
        );
        
        array_push($order->items, $item);
    }
    
    // Create the order
    if($order->create()){
        // Clear the cart
        $cart->clearCart();
        
        // Set response code - 201 created
        http_response_code(201);
        
        // Tell the user
        echo json_encode(array(
            "message" => "Order was created.",
            "order_id" => $order->id,
            "total_amount" => $order->total_amount
        ));
    }
    else{
        // Set response code - 503 service unavailable
        http_response_code(503);
        
        // Tell the user
        echo json_encode(array("message" => "Unable to create order."));
    }
}
else{
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to create order. Cart is empty."));
}
?>

