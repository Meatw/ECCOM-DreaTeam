<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Include database and product model
include_once '../../config/database.php';
include_once '../../models/Product.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate product object
$product = new Product($db);

// Set ID property of record to read
$product->id = isset($_GET['id']) ? $_GET['id'] : die();

// Read the details of product
if($product->readOne()){
    // Create array
    $product_arr = array(
        "id" => $product->id,
        "name" => $product->name,
        "description" => $product->description,
        "price" => $product->price,
        "category_id" => $product->category_id,
        "category_name" => $category_name,
        "seller_id" => $product->seller_id,
        "seller_name" => $seller_name,
        "image" => $product->image
    );
    
    // Set response code - 200 OK
    http_response_code(200);
    
    // Make it json format
    echo json_encode($product_arr);
}
else{
    // Set response code - 404 Not found
    http_response_code(404);
    
    // Tell the user product does not exist
    echo json_encode(array("message" => "Product does not exist."));
}
?>

