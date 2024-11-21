<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include database connection
include('../db.php');

// Check database connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Get the JSON data from the request body
$data = json_decode(file_get_contents("php://input"));

// Log received data for debugging (you can remove this after debugging)
error_log(print_r($data, true));

// Validate input data
if (isset($data->id) && isset($data->name) && isset($data->price) && isset($data->description) && isset($data->stock)) {
    $id = $data->id;
    $name = $conn->real_escape_string($data->name);
    $price = floatval($data->price);
    $description = $conn->real_escape_string($data->description);
    $stock = intval($data->stock); // Ensure stock is an integer
    $image = isset($data->image) ? $conn->real_escape_string($data->image) : null;

    // Prepare the update query
    if ($image) {
        $query = "UPDATE products SET name = ?, price = ?, description = ?, stock = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdsssi", $name, $price, $description, $stock, $image, $id); // Bind with image
    } else {
        $query = "UPDATE products SET name = ?, price = ?, description = ?, stock = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdssi", $name, $price, $description, $stock, $id); // Without image
    }

    if ($stmt) {
        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            echo json_encode(["message" => "Product updated successfully"]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["message" => "Failed to update product"]);
        }
        $stmt->close();
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "Failed to prepare statement"]);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["message" => "Invalid input, all fields are required."]);
}

// Close the database connection
$conn->close();
?>
