<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Start session
session_start();

// Destroy session
session_destroy();

// Set response code - 200 OK
http_response_code(200);

// Tell the user
echo json_encode(array("message" => "Logged out successfully."));
?>

