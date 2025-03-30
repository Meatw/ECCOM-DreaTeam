<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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
    echo json_encode(array("message" => "Access denied. Only customers can view cart."));
    exit;
}

// Set user ID
$cart->user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $cart->getItems();
$num = $stmt->rowCount();

// Check if more than 0 record found
if($num > 0){
    // Cart array
    $cart_arr = array();
    $cart_arr["items"] = array();
    $total = 0;
    
    // Retrieve table contents
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        
        $item = array(
            "id" => $id,
            "product_id" => $product_id,
            "name" => $name,
            "price" => $price,
            "quantity" => $quantity,
            "subtotal" => $price * $quantity,
            "image" => $image
        );
        
        $total += ($price * $quantity);
        
        array_push($cart_arr["items"], $item);
    }
    
    $cart_arr["total"] = $total;
    
    // Set response code - 200 OK
    http_response_code(200);
    
    // Show cart data
    echo json_encode($cart_arr);
}
else{
    // Set response code - 200 OK
    http_response_code(200);
    
    // Tell the user cart is empty
    echo json_encode(array("message" => "Cart is empty.", "items" => array(), "total" => 0));
}
?>

