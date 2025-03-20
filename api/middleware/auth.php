<?php
// Include JWT utility
include_once '../../utils/jwt.php';

// Check if user is authenticated
function isAuthenticated() {
    // Get headers
    $headers = getallheaders();
    
    // Get JWT token from Authorization header
    $jwt = isset($headers['Authorization']) ? $headers['Authorization'] : "";
    
    // If JWT is not empty
    if ($jwt) {
        // Validate JWT
        return validateJWT($jwt);
    }
    
    return false;
}

// Check if user is authorized (has required role)
function isAuthorized($required_roles = array()) {
    // Get headers
    $headers = getallheaders();
    
    // Get JWT token from Authorization header
    $jwt = isset($headers['Authorization']) ? $headers['Authorization'] : "";
    
    // If JWT is not empty
    if ($jwt) {
        try {
            // Decode JWT
            $decoded = decodeJWT($jwt);
            
            // If no specific roles required, any authenticated user is authorized
            if (empty($required_roles)) {
                return true;
            }
            
            // Check if user has required role
            return in_array($decoded->data->user_type, $required_roles);
        } catch (Exception $e) {
            return false;
        }
    }
    
    return false;
}

// Get authenticated user ID
function getAuthUserId() {
    // Get headers
    $headers = getallheaders();
    
    // Get JWT token from Authorization header
    $jwt = isset($headers['Authorization']) ? $headers['Authorization'] : "";
    
    // If JWT is not empty
    if ($jwt) {
        try {
            // Decode JWT
            $decoded = decodeJWT($jwt);
            
            // Return user ID
            return $decoded->data->id;
        } catch (Exception $e) {
            return null;
        }
    }
    
    return null;
}

// Get authenticated user type
function getAuthUserType() {
    // Get headers
    $headers = getallheaders();
    
    // Get JWT token from Authorization header
    $jwt = isset($headers['Authorization']) ? $headers['Authorization'] : "";
    
    // If JWT is not empty
    if ($jwt) {
        try {
            // Decode JWT
            $decoded = decodeJWT($jwt);
            
            // Return user type
            return $decoded->data->user_type;
        } catch (Exception $e) {
            return null;
        }
    }
    
    return null;
}
?>