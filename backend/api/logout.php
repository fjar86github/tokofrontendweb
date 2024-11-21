<?php
// Allow requests from localhost on port 8100 (Ionic app)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
session_start(); // Start the session

// Clear all session variables
$_SESSION = [];

// If you want to destroy the session completely
if (session_id() != '' || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/'); // Delete the session cookie
}

// Destroy the session
session_destroy();

// Return a JSON response indicating successful logout
header('Content-Type: application/json');
echo json_encode(['message' => 'Logout successful']);
?>
