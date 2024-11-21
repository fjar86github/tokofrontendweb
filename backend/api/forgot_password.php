<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200); // OK
    exit(); // No further action required
}

include('../db.php');
// Database connection
//$conn = new mysqli("localhost", "root", "", "online_store_db");

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    if (isset($data['email'])) {
        $email = $conn->real_escape_string($data['email']);

        // Check if the email exists
        $checkQuery = $conn->prepare("SELECT username FROM users WHERE email = ?");
        $checkQuery->bind_param("s", $email);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows > 0) {
            // Fetch the existing user information
            $user = $result->fetch_assoc();
            $username = $user['username'];

            // Hash the username to use as the new password
            $new_password = password_hash($username, PASSWORD_BCRYPT);

            // Update the password in the database
            $updateQuery = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateQuery->bind_param("ss", $new_password, $email);
            if ($updateQuery->execute()) {
                // Password reset successful
                echo json_encode([
                    "status" => "success",
                    "message" => "Password has been reset to the username.",
                    "username" => $username // return the username for confirmation
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update password."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Email not found"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid input"]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Invalid request method. Please use POST."]);
}

// Close the database connection
$conn->close();
?>