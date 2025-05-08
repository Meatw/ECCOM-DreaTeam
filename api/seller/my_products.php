<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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
    echo json_encode(array("message" => "Access denied. Only sellers can view their products."));
    exit;
}

// Set seller ID
$product->seller_id = $_SESSION['user_id'];

// Query products
$stmt = $product->readBySeller();
$num = $stmt->rowCount();

// Check if more than 0 record found
if($num > 0){
    // Products array
    $products_arr = array();
    $products_arr["records"] = array();
    
    // Retrieve table contents
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        
        $product_item = array(
            "id" => $id,
            "name" => $name,
            "description" => $description,
            "price" => $price,
            "category_name" => $category_name,
            "image" => $image,
            "created" => $created
        );
        
        array_push($products_arr["records"], $product_item);
    }
    
    // Set response code - 200 OK
    http_response_code(200);
    
    // Show products data
    echo json_encode($products_arr);
}
else{
    // Set response code - 200 OK
    http_response_code(200);
    
    // Tell the user no products found
    echo json_encode(array("message" => "No products found.", "records" => array()));
}
?>

