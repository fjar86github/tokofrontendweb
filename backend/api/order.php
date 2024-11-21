<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../db.php');
// Database connection settings
//$servername = "localhost"; // e.g., "localhost"
//$username = "root"; // your database username
//$password = ""; // your database password
//$dbname = "online_store_db"; // your database name

// Create connection
//$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Function to create a new order and update product stock
function createOrder($conn, $productId, $quantity) {
    // Begin transaction
    $conn->begin_transaction();
    try {
        // Insert order into orders table
        $stmt = $conn->prepare("INSERT INTO orders (product_id, quantity) VALUES (?, ?)");
        $stmt->bind_param("ii", $productId, $quantity);

        if (!$stmt->execute()) {
            throw new Exception("Error placing order: " . $stmt->error);
        }

        // Update the product stock in products table
        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
        $stmt->bind_param("iii", $quantity, $productId, $quantity);

        if (!$stmt->execute()) {
            throw new Exception("Error updating product stock: " . $stmt->error);
        }

        // Check if the stock was actually updated
        if ($stmt->affected_rows === 0) {
            throw new Exception("Insufficient stock for product ID: " . $productId);
        }

        // Commit transaction
        $conn->commit();

        return ["status" => "success", "message" => "Order placed successfully."];
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        return ["status" => "error", "message" => $e->getMessage()];
    }
}

// Function to retrieve all orders
function getOrders($conn) {
    $result = $conn->query("SELECT * FROM orders");
    $orders = [];

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    return $orders;
}

// Handle the request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    // Get the POST data
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['productId']) && isset($data['quantity'])) {
        $productId = $data['productId'];
        $quantity = $data['quantity'];

        // Create a new order
        $response = createOrder($conn, $productId, $quantity);
        echo json_encode($response);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid input."]);
    }
} elseif ($requestMethod === 'GET') {
    // Get all orders
    $orders = getOrders($conn);
    echo json_encode($orders);
} else {
    echo json_encode(["status" => "error", "message" => "Method not allowed."]);
}

$conn->close(); // Close the database connection
?>
