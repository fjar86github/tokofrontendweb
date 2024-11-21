<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Adjust to your specific origin if needed
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
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input data
    $name = isset($data['name']) ? $conn->real_escape_string($data['name']) : null;
    $price = isset($data['price']) ? $conn->real_escape_string($data['price']) : null;
    $description = isset($data['description']) ? $conn->real_escape_string($data['description']) : null;
    $stock = isset($data['stock']) ? $conn->real_escape_string($data['stock']) : null;
    $image = isset($data['image']) ? $conn->real_escape_string($data['image']) : null;

    // Ensure all required fields are provided
    if ($name && $price !== null && $description && $stock !== null && $image) {
        $sql = "INSERT INTO products (name, price, description, stock, image) VALUES ('$name', '$price', '$description', '$stock', '$image')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Product added successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error adding product: " . $conn->error]);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "All fields are required."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}

$conn->close();
?>
