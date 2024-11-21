<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
include('../db.php');
// Koneksi database
//$conn = new mysqli("localhost", "root", "", "online_store_db");

// Cek koneksi
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["message" => "Connection failed: " . $conn->connect_error]));
}

// Mengambil semua produk
$query = "SELECT * FROM products";
$result = $conn->query($query);

if ($result === false) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["message" => "Error executing query: " . $conn->error]));
}

// Menyimpan produk dalam array
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Memeriksa apakah produk ditemukan
if (empty($products)) {
    http_response_code(404); // Not Found
    echo json_encode(["message" => "No products found"]);
} else {
    // Mengembalikan produk
    echo json_encode($products);
}

$conn->close();
?>
