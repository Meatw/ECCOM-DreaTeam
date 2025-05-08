<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Make sure data is not empty
if(
    !empty($data->email) &&
    !empty($data->password)
){
    // Set user property values
    $user->email = $data->email;
    $user->password = $data->password;
    
    // Attempt to login
    if($user->login()){
        // Create session
        session_start();
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        $_SESSION['role'] = $user->role;
        
        // Set response code - 200 OK
        http_response_code(200);
        
        // Tell the user
        echo json_encode(array(
            "message" => "Login successful.",
            "user" => array(
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email,
                "role" => $user->role
            )
        ));
    }
    else{
        // Set response code - 401 Unauthorized
        http_response_code(401);
        
        // Tell the user
        echo json_encode(array("message" => "Login failed. Invalid email or password."));
    }
}
else{
    // Set response code - 400 bad request
    http_response_code(400);
    
    // Tell the user
    echo json_encode(array("message" => "Unable to login. Data is incomplete."));
}
?>

