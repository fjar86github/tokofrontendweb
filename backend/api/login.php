<?php
session_start();
include('../db.php');

// Allow requests from localhost on port 8100 (Ionic app)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Allow credentials

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

// Ensure the method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = isset($data['username']) ? $conn->real_escape_string($data['username']) : '';
    $password = isset($data['password']) ? $data['password'] : '';

    // Validate input
    if (empty($username) || empty($password)) {
        echo json_encode(["message" => "Username or password cannot be empty"]);
        exit();
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variable
            $_SESSION['user_id'] = $user['id']; // Store user ID in session

            // Return success response
            echo json_encode(["message" => "Login successful", "user_id" => $user['id']]);
        } else {
            echo json_encode(["message" => "Invalid credentials"]);
        }
    } else {
        echo json_encode(["message" => "User not found"]);
    }

    $stmt->close();
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(["message" => "Invalid request method"]);
}

$conn->close();
?>
