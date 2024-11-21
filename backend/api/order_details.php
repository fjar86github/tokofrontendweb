<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
include('../db.php'); // Pastikan koneksi database sudah benar

// Memeriksa apakah koneksi ke database berhasil
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]));
}

// Fungsi untuk mengambil data pesanan berdasarkan user_id dan order_id
function getOrderDetails($conn, $userId, $orderId) {
    // Menyiapkan query SQL untuk mendapatkan order_id, order_date, product details dan total_price
    $stmt = $conn->prepare("SELECT
                                o.id AS order_id,
                                o.order_date AS order_date,
                                p.id AS product_id,
                                p.name AS product_name,
                                p.price AS product_price,
                                o.quantity,
                                (p.price * o.quantity) AS total_price
                            FROM
                                orders o
                            JOIN
                                products p ON o.product_id = p.id
                            WHERE
                                o.user_id = ? AND o.id = ?");

    if (!$stmt) {
        // Menambahkan pesan error jika query tidak berhasil disiapkan
        die(json_encode(["status" => "error", "message" => "Query preparation failed: " . $conn->error]));
    }

    $stmt->bind_param("ii", $userId, $orderId);  // Mengikat parameter user_id dan order_id
    $stmt->execute();
    $result = $stmt->get_result();

    // Mengambil hasil dan memformat dalam array asosiatif
    $orderDetails = [];
    while ($row = $result->fetch_assoc()) {
        $orderDetails[] = $row;  // Menyimpan setiap produk dalam array orderDetails
    }

    return $orderDetails;
}

// Menangani metode permintaan
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'GET') {
    // Memeriksa apakah user_id dan order_id diberikan dalam query string
    if (isset($_GET['user_id']) && isset($_GET['order_id'])) {
        $userId = $_GET['user_id'];  // Mendapatkan user_id dari query string
        $orderId = $_GET['order_id']; // Mendapatkan order_id dari query string
        
        // Memeriksa validitas parameter
        if (!is_numeric($userId) || !is_numeric($orderId)) {
            echo json_encode(["status" => "error", "message" => "Invalid parameters. 'user_id' and 'order_id' must be numeric."]);
            exit;
        }

        // Mendapatkan detail pesanan berdasarkan user_id dan order_id
        $orderDetails = getOrderDetails($conn, $userId, $orderId);

        if (!empty($orderDetails)) {
            // Jika data ditemukan, tampilkan hasilnya
            echo json_encode(["status" => "success", "data" => $orderDetails]);
        } else {
            // Jika tidak ada data, tampilkan pesan error
            echo json_encode(["status" => "error", "message" => "Order not found or invalid parameters."]);
        }
    } else {
        // Jika parameter user_id dan order_id tidak ada
        echo json_encode(["status" => "error", "message" => "user_id and order_id are required."]);
    }
} else {
    // Jika metode selain GET digunakan
    echo json_encode(["status" => "error", "message" => "Method not allowed. Only GET is allowed."]);
}

$conn->close(); // Menutup koneksi database
?>
