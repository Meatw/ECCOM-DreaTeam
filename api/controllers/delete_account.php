<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and user model
include_once '../../config/Database.php';
include_once '../../models/User.php';
include_once '../../utils/jwt.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate user object
$user = new User($db);

// Get JWT token from the request
$headers = getallheaders();
$jwt = isset($headers['Authorization']) ? $headers['Authorization'] : "";

// If JWT is not empty
if ($jwt) {
    // If decode succeed, show user details
    try {
        // Decode jwt
        $decoded = decodeJWT($jwt);
        
        // Set user ID
        $user->id = $decoded->data->id;
        
        // Delete the user
        if ($user->delete()) {
            // Set response code - 200 OK
            http_response_code(200);
            
            // Tell the user
            echo json_encode(array("message" => "User account was deleted."));
        } else {
            // Set response code - 503 service unavailable
            http_response_code(503);
            
            // Tell the user
            echo json_encode(array("message" => "Unable to delete user account."));
        }
    } catch (Exception $e) {
        // Set response code - 401 Unauthorized
        http_response_code(401);
        
        // Tell the user access denied
        echo json_encode(array("message" => "Access denied.", "error" => $e->getMessage()));
    }
} else {
    // Set response code - 401 Unauthorized
    http_response_code(401);
    
    // Tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
?>