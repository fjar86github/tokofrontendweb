<?php
// Allow requests from localhost on port 8100 (Ionic app)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true'); // Allow credentials

session_start();
include('../db.php');

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Please log in to access this page.']);
    exit();
}

// Handle CSV file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        // Open the CSV file for reading
        if (($handle = fopen($file, 'r')) !== false) {
            // Skip the header row
            fgetcsv($handle);

            // Prepare statements for each table
            $stmtUser = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmtProduct = $conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
            $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity) VALUES (?, ?, ?)");

            // Array to collect messages for success and errors
            $messages = [];

            while (($data = fgetcsv($handle)) !== false) {
                // Handle users
                if (count($data) >= 3) { // Ensure there are at least 3 columns for users
                    $username = $data[0];
                    $password = $data[1]; // Use the already hashed password directly
                    $email = $data[2];

                    // Bind parameters and execute for users
                    if ($stmtUser->bind_param("sss", $username, $password, $email) && $stmtUser->execute()) {
                        $messages[] = "User '$username' imported successfully.";
                    } else {
                        $messages[] = "Error importing user '$username': " . $stmtUser->error;
                    }
                }

                // Handle products (assuming the next columns are for products)
                if (count($data) >= 5) { // Ensure there are enough columns for products
                    $productName = $data[3];
                    $productDescription = $data[4];
                    $productPrice = $data[5];
                    $productStock = $data[6];
                    $productImage = $data[7] ?? null; // Optional image column

                    // Bind parameters and execute for products
                    if ($stmtProduct->bind_param("ssdis", $productName, $productDescription, $productPrice, $productStock, $productImage) && $stmtProduct->execute()) {
                        $messages[] = "Product '$productName' imported successfully.";
                    } else {
                        $messages[] = "Error importing product '$productName': " . $stmtProduct->error;
                    }
                }

                // Handle orders (assuming the last columns are for orders)
                if (count($data) >= 8) { // Ensure there are enough columns for orders
                    $userId = $data[9]; // Adjust based on CSV structure
                    $productId = $data[10]; // Adjust based on CSV structure
                    $quantity = $data[11]; // Adjust based on CSV structure

                    // Bind parameters and execute for orders
                    if ($stmtOrder->bind_param("iii", $userId, $productId, $quantity) && $stmtOrder->execute()) {
                        $messages[] = "Order for user ID '$userId' and product ID '$productId' imported successfully.";
                    } else {
                        $messages[] = "Error importing order for user ID '$userId': " . $stmtOrder->error;
                    }
                }
            }

            fclose($handle);
            header('Content-Type: application/json');
            echo json_encode(['messages' => $messages]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Error opening the CSV file.']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'File upload error: ' . $_FILES['csv_file']['error']]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'No CSV file uploaded.']);
}
?>
