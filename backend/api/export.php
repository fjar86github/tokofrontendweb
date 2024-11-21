<?php
session_start();
include('../db.php');

// Allow requests from localhost on port 8100 (Ionic app)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

// Check if the request method is POST for login
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
            
            // Redirect or respond with success message
            echo json_encode(["message" => "Login successful"]);
            exit(); // End the script after successful login
        } else {
            echo json_encode(["message" => "Invalid credentials"]);
            exit();
        }
    } else {
        echo json_encode(["message" => "User not found"]);
        exit();
    }
}

// Check if the user is logged in for export functionality
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    //echo json_encode(['message' => 'Please log in to access this page.']);
    //exit();
}

// Set the content type and filename for the CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export_data.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Export Users
fputcsv($output, ['Username', 'Email']); // Header for users
$userQuery = "SELECT username, email FROM users";
if ($result = $conn->query($userQuery)) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

// Export Products
fputcsv($output, []); // Empty line for separation
fputcsv($output, ['Name', 'Description', 'Price', 'Stock', 'Image']); // Header for products
$productQuery = "SELECT name, description, price, stock, image FROM products";
if ($result = $conn->query($productQuery)) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

// Export Orders
fputcsv($output, []); // Empty line for separation
fputcsv($output, ['User ID', 'Product ID', 'Quantity', 'Order Date']); // Header for orders
$orderQuery = "SELECT user_id, product_id, quantity, order_date FROM orders";
if ($result = $conn->query($orderQuery)) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

// Close output stream
fclose($output);
exit();
?>