<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and business model
include_once '../../config/database.php';
include_once '../../models/Business.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate business object
$business = new Business($db);

// Check if user is logged in and is an admin
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Only admins can approve businesses."));
    exit;
}

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if(
    !empty($data->id) &&
    !empty($data->status)
){
    // Set business property values
    $business->id = $data->id;
    $business->status = $data->status; // 'approved' or 'rejected'
    
    // Update business status
    if($business->updateStatus()){
        // Set response code - 200 OK
        http_response_code(200);
        
        // Tell the user
        echo json_encode(array("message" => "Business status was updated."));
    }
    else{
        // Set response code - 503 service unavailable
        http_response_code(503);
        
        // Tell the user
        echo json_encode(array("message" => "Unable to update business status."));
    }
}
else{
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to update business status. Data is incomplete."));
}
?>

