<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and user model
include_once '../../config/database.php';
include_once '../../models/user.php';
include_once '../../models/seller.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate user object
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (
    !empty($data->fullName) &&
    !empty($data->email) &&
    !empty($data->password) &&
    !empty($data->accountType)
) {
    // Check if email already exists
    if ($user->emailExists($data->email)) {
        // Set response code - 400 Bad Request
        http_response_code(400);
        
        // Tell the user email already exists
        echo json_encode(array("status" => "error", "message" => "Email already exists"));
        exit;
    }
    
    // Set user property values
    $user->full_name = $data->fullName;
    $user->email = $data->email;
    $user->password = password_hash($data->password, PASSWORD_BCRYPT);
    $user->account_type = $data->accountType;
    $user->created_at = date('Y-m-d H:i:s');
    
    // Create the user
    if ($user->create()) {
        // If user is a seller, create seller profile
        if ($data->accountType === 'seller') {
            // Check if seller data is provided
            if (
                !empty($data->storeName) &&
                !empty($data->storeDescription) &&
                !empty($data->phoneNumber)
            ) {
                // Instantiate seller object
                $seller = new Seller($db);
                
                // Set seller property values
                $seller->user_id = $user->id;
                $seller->store_name = $data->storeName;
                $seller->store_description = $data->storeDescription;
                $seller->phone_number = $data->phoneNumber;
                $seller->created_at = date('Y-m-d H:i:s');
                
                // Create the seller profile
                if (!$seller->create()) {
                    // Set response code - 503 Service Unavailable
                    http_response_code(503);
                    
                    // Tell the user
                    echo json_encode(array("status" => "error", "message" => "Unable to create seller profile"));
                    exit;
                }
            } else {
                // Set response code - 400 Bad Request
                http_response_code(400);
                
                // Tell the user
                echo json_encode(array("status" => "error", "message" => "Unable to create seller profile. Data is incomplete."));
                exit;
            }
        }
        
        // Set response code - 201 Created
        http_response_code(201);
        
        // Tell the user
        echo json_encode(array("status" => "success", "message" => "User was created"));
    } else {
        // Set response code - 503 Service Unavailable
        http_response_code(503);
        
        // Tell the user
        echo json_encode(array("status" => "error", "message" => "Unable to create user"));
    }
} else {
    // Set response code - 400 Bad Request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("status" => "error", "message" => "Unable to create user. Data is incomplete."));
}
?>