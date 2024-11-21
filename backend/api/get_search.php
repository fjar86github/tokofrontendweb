<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include('../db.php');
// Konfigurasi koneksi database
//$conn = new mysqli("localhost", "root", "", "online_store_db");

// Cek koneksi
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Memastikan metode permintaan adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dari input JSON
    $data = json_decode(file_get_contents('php://input'), true);
    $query = isset($data['query']) ? trim($data['query']) : '';

    // Validasi input
    if (empty($query)) {
        http_response_code(400); // Bad Request
        echo json_encode(["message" => "Query cannot be empty"]);
        exit();
    }

    // Menyiapkan statement untuk mencari produk
    $stmt = $conn->prepare("SELECT * FROM products WHERE Name LIKE ?");
    $searchTerm = "%" . $conn->real_escape_string($query) . "%"; // Menyiapkan parameter pencarian
    $stmt->bind_param("s", $searchTerm); // Mengikat parameter

    // Mengeksekusi statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        // Mengembalikan hasil pencarian
        echo json_encode($products);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "Query execution failed: " . $stmt->error]);
    }

    $stmt->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Invalid request method"]);
}

$conn->close();
?>
