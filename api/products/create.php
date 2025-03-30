<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and product model
include_once '../../config/database.php';
include_once '../../models/Product.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate product object
$product = new Product($db);

// Check if user is logged in and is a seller
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller'){
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Only sellers can create products."));
    exit;
}

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if(
    !empty($data->name) &&
    !empty($data->description) &&
    !empty($data->price) &&
    !empty($data->category_id)
){
    // Set product property values
    $product->name = $data->name;
    $product->description = $data->description;
    $product->price = $data->price;
    $product->category_id = $data->category_id;
    $product->seller_id = $_SESSION['user_id'];
    $product->image = isset($data->image) ? $data->image : "";
    
    // Create the product
    if($product->create()){
        // Set response code - 201 created
        http_response_code(201);
        
        // Tell the user
        echo json_encode(array("message" => "Product was created."));
    }
    else{
        // Set response code - 503 service unavailable
        http_response_code(503);
        
        // Tell the user
        echo json_encode(array("message" => "Unable to create product."));
    }
}
else{
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
}
?>

