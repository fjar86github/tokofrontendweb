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
include('../db.php');
// Database connection settings
//$servername = "localhost";
//$username = "root";
//$password = "";
//$dbname = "online_store_db";

//$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Get JSON data
$data = json_decode(file_get_contents("php://input"));

// Validate input data
if (isset($data->id) && isset($data->name) && isset($data->price) && isset($data->description) && isset($data->stock) && isset($data->image)) {
    $id = $data->id;
    $name = $conn->real_escape_string($data->name);
    $price = $data->price;
    $description = $conn->real_escape_string($data->description);
    $stock = isset($data->stock) ? $data->stock : null; // Optional stock field
    $image = isset($data->image) ? $conn->real_escape_string($data->image) : null; // Optional image field

    // Prepare update query
    $query = "UPDATE products SET name = ?, price = ?, description = ?, stock = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sdissi", $name, $price, $description, $stock, $image, $id);
        
        // Execute query and check result
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

// Close connection
$conn->close();
?>