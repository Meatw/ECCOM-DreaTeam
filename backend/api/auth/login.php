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
    $passwordInput = $data->password;

    // Check if user exists and password is correct
    $stmt = $user->login();
    
    if ($stmt->rowCount() > 0) {
        // Get user data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password
        if (password_verify($passwordInput, $row['password'])) {
            // Generate JWT token
            $token = generateJWT($row);
            
            // Set response code - 200 OK
            http_response_code(200);
            
            // Return success response with token
            echo json_encode(
                array(
                    "status" => "success",
                    "message" => "Login successful",
                    "token" => $token,
                    "user" => array(
                        "id" => $row['id'],
                        "fullName" => $row['full_name'],
                        "email" => $row['email'],
                        "accountType" => $row['account_type']
                    )
                )
            );
        } else {
            // Set response code - 401 Unauthorized
            http_response_code(401);
            
            // Tell the user login failed
            echo json_encode(array("status" => "error", "message" => "Invalid credentials"));
        }
    } else {
        // Set response code - 401 Unauthorized
        http_response_code(401);
        
        // Tell the user login failed
        echo json_encode(array("status" => "error", "message" => "Invalid credentials"));
    }
} else {
    // Set response code - 400 Bad Request
    http_response_code(400);
    
    // Tell the user data is incomplete
    echo json_encode(array("status" => "error", "message" => "Unable to login. Data is incomplete."));
}

// Function to generate JWT token
function generateJWT($user) {
    $secret_key = "YOUR_SECRET_KEY";
    $issuer_claim = "THE_ISSUER"; // this can be the servername
    $audience_claim = "THE_AUDIENCE";
    $issuedat_claim = time(); // issued at
    $notbefore_claim = $issuedat_claim; //not before
    $expire_claim = $issuedat_claim + 3600; // expire time (1 hour)
    
    $token = array(
        "iss" => $issuer_claim,
        "aud" => $audience_claim,
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
        "exp" => $expire_claim,
        "data" => array(
            "id" => $user['id'],
            "fullName" => $user['full_name'],
            "email" => $user['email'],
            "accountType" => $user['account_type']
        )
    );
    
    // Encode the JWT token
    $jwt = base64_encode(json_encode($token));
    
    return $jwt;
}
?>