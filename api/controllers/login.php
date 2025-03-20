<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and user model
include_once '../../config/Database.php';
include_once '../../models/User.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate user object
$user = new User($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if (
    !empty($data->email) &&
    !empty($data->password)
) {
    // Set user property values
    $user->email = $data->email;
    $user->password = $data->password;
    
    // Attempt to login
    if ($user->login()) {
        // Generate JWT token
        include_once '../../utils/jwt.php';
        $jwt = generateJWT($user->id, $user->name, $user->email, $user->user_type);
        
        // Set response code - 200 OK
        http_response_code(200);
        
        // Tell the user
        echo json_encode(array(
            "message" => "Login successful.",
            "token" => $jwt,
            "user" => array(
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "user_type" => $user->user_type
            )
        ));
    } else {
        // Set response code - 401 Unauthorized
        http_response_code(401);
        
        // Tell the user
        echo json_encode(array("message" => "Invalid email or password."));
    }
} else {
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to login. Data is incomplete."));
}
?>