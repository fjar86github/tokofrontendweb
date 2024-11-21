<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../db.php'); // Ensure the database connection is correct

// Check if the database connection is established
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]));
}

// Function to create a new order and update product stock
function createOrder($conn, $userId, $productId, $quantity, $orderDate) {
    // Begin transaction
    mysqli_begin_transaction($conn);
    try {
        // Insert order into orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, order_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $userId, $productId, $quantity, $orderDate);

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
        mysqli_commit($conn);

        return ["status" => "success", "message" => "Order placed successfully."];
    } catch (Exception $e) {
        // Rollback transaction in case of error
        mysqli_rollBack($conn);
        return ["status" => "error", "message" => $e->getMessage()];
    }
}

// Function to retrieve orders based on user_id
function getOrdersByUserId($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
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

    if (isset($data['user_id']) && isset($data['product_id']) && isset($data['quantity']) && isset($data['order_date'])) {
        $userId = $data['user_id']; // Get userId from input
        $productId = $data['product_id'];
        $quantity = $data['quantity'];
        $orderDate = $data['order_date']; // Get the order_date from input (it should be in a valid date format)

        // Create a new order
        $response = createOrder($conn, $userId, $productId, $quantity, $orderDate);
        echo json_encode($response);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid input. Please provide userId, productId, quantity, and orderDate."]);
    }
} elseif ($requestMethod === 'GET') {
    // Check if user_id is provided in the query string
    if (isset($_GET['user_id'])) {
        $userId = $_GET['user_id']; // Get user_id from query string
        // Get orders for the specified user_id
        $orders = getOrdersByUserId($conn, $userId);
        echo json_encode($orders);
    } else {
        echo json_encode(["status" => "error", "message" => "user_id is required."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Method not allowed."]);
}

$conn->close(); // Close the database connection
?>
