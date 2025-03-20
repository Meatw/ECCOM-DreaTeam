<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
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
        
        // Get posted data
        $data = json_decode(file_get_contents("php://input"));
        
        // Make sure data is not empty and user ID matches
        if (
            !empty($data->name) &&
            $decoded->data->id
        ) {
            // Set user property values
            $user->id = $decoded->data->id;
            $user->name = $data->name;
            $user->phone = !empty($data->phone) ? $data->phone : "";
            $user->address = !empty($data->address) ? $data->address : "";
            
            // Set password if provided
            if (!empty($data->password)) {
                $user->password = $data->password;
            }
            
            // Set profile image if provided
            if (!empty($data->profile_image)) {
                $user->profile_image = $data->profile_image;
            }
            
            // Update the user
            if ($user->update()) {
                // Set response code - 200 OK
                http_response_code(200);
                
                // Tell the user
                echo json_encode(array("message" => "User profile was updated."));
            } else {
                // Set response code - 503 service unavailable
                http_response_code(503);
                
                // Tell the user
                echo json_encode(array("message" => "Unable to update user profile."));
            }
        } else {
            // Set response code - 400 bad request
            http_response_code(400);
            
            // Tell the user
            echo json_encode(array("message" => "Unable to update user profile. Data is incomplete."));
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