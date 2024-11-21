<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include('../db.php');

// Koneksi database
//$conn = new mysqli("localhost", "root", "", "online_store_db");

// Cek koneksi
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Memastikan metode permintaan adalah DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Mengambil ID dari query string
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Validasi ID
    if ($id > 0) {
        // Menyiapkan pernyataan SQL untuk menghapus data
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt === false) {
            http_response_code(500); // Internal Server Error
            die(json_encode(["message" => "Error preparing statement: " . $conn->error]));
        }
        
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                http_response_code(200); // OK
                echo json_encode(["message" => "Product deleted successfully"]);
            } else {
                http_response_code(404); // Not Found
                echo json_encode(["message" => "Product not found"]);
            }
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["message" => "Error deleting product: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "Invalid product ID"]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Invalid request method. Please use DELETE."]);
}

$conn->close();
?>
