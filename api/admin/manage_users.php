<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and user model
include_once '../../config/database.php';
include_once '../../models/User.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate user object
$user = new User($db);

// Check if user is logged in and is an admin
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    // Set response code - 403 Forbidden
    http_response_code(403);
    
    // Tell the user
    echo json_encode(array("message" => "Access denied. Only admins can manage users."));
    exit;
}

// Query users
$stmt = $user->readAll();
$num = $stmt->rowCount();

// Check if more than 0 record found
if($num > 0){
    // Users array
    $users_arr = array();
    $users_arr["records"] = array();
    
    // Retrieve table contents
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        
        $user_item = array(
            "id" => $id,
            "username" => $username,
            "email" => $email,
            "role" => $role,
            "created" => $created
        );
        
        array_push($users_arr["records"], $user_item);
    }
    
    // Set response code - 200 OK
    http_response_code(200);
    
    // Show users data
    echo json_encode($users_arr);
}
else{
    // Set response code - 404 Not found
    http_response_code(404);
    
    // Tell the user no users found
    echo json_encode(array("message" => "No users found."));
}
?>

