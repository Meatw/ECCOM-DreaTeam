<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database
include_once '../../config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Check if user is logged in and is a seller
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller'){
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Only sellers can view top selling products."));
    exit;
}

// Query to get top selling products for this seller
$query = "SELECT p.id, p.name, p.price, p.image, 
            SUM(oi.quantity) as total_sold,
            SUM(oi.quantity * oi.price) as total_revenue
          FROM products p
          JOIN order_items oi ON p.id = oi.product_id
          JOIN orders o ON oi.order_id = o.id
          WHERE p.seller_id = ?
          GROUP BY p.id
          ORDER BY total_sold DESC
          LIMIT 10";

// Prepare query statement
$stmt = $db->prepare($query);

// Bind seller ID
$stmt->bindParam(1, $_SESSION['user_id']);

// Execute query
$stmt->execute();

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
            "price" => $price,
            "image" => $image,
            "total_sold" => $total_sold,
            "total_revenue" => $total_revenue
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
    echo json_encode(array("message" => "No sales data found.", "records" => array()));
}
?>

