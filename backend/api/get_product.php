<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include('../db.php');
// Koneksi database
//$conn = new mysqli("localhost", "root", "", "online_store_db");

// Cek koneksi
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Memastikan metode permintaan adalah GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Validasi ID
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        if ($stmt === false) {
            http_response_code(500); // Internal Server Error
            die(json_encode(["message" => "Error preparing statement: " . $conn->error]));
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            echo json_encode($product);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["message" => "Product not found"]);
        }

        $stmt->close();
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "Invalid product ID"]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Invalid request method"]);
}

$conn->close();
?>
