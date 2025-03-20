<?php
// Include JWT library
require_once '../../vendor/autoload.php';
use \Firebase\JWT\JWT;

// Generate JWT token
function generateJWT($id, $name, $email, $user_type) {
    $secret_key = JWT_SECRET;
    $issuer_claim = "quickbuy.com"; // this can be the domain name
    $audience_claim = "quickbuy_users";
    $issuedat_claim = time(); // issued at
    $notbefore_claim = $issuedat_claim; // not before
    $expire_claim = $issuedat_claim + JWT_EXPIRATION; // expire time
    
    $token = array(
        "iss" => $issuer_claim,
        "aud" => $audience_claim,
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
        "exp" => $expire_claim,
        "data" => array(
            "id" => $id,
            "name" => $name,
            "email" => $email,
            "user_type" => $user_type
        )
    );
    
    // Generate JWT
    return JWT::encode($token, $secret_key);
}

// Decode JWT token
function decodeJWT($jwt) {
    $secret_key = JWT_SECRET;
    
    // Remove 'Bearer ' from token if present
    if (strpos($jwt, 'Bearer ') === 0) {
        $jwt = substr($jwt, 7);
    }
    
    // Decode token
    return JWT::decode($jwt, $secret_key, array('HS256'));
}

// Validate JWT token
function validateJWT($jwt) {
    try {
        $decoded = decodeJWT($jwt);
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>