<?php
// Set headers to allow CORS and specify content type
header("Access-Control-Allow-Origin: http://localhost:8100"); // Adjust to your frontend origin
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

include('../db.php'); // Include your database connection file

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 204 No Content");
    exit();
}

// Query to get sales statistics
$sql = "
    SELECT 
        p.id AS product_id,
        p.name AS product_name,
        COALESCE(SUM(o.quantity), 0) AS total_quantity_sold,
        COALESCE(SUM(o.quantity * p.price), 0) AS total_sales,
        COUNT(o.id) AS total_orders
    FROM 
        products p
    LEFT JOIN 
        orders o ON p.id = o.product_id
    GROUP BY 
        p.id
    ORDER BY 
        total_sales DESC
";

$result = $conn->query($sql);

$salesStatistics = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $salesStatistics[] = $row;
    }
    // Return the sales statistics as a JSON response
    echo json_encode(['data' => $salesStatistics]);
} else {
    echo json_encode(['message' => 'Error fetching sales statistics: ' . $conn->error]);
}

$conn->close(); // Close the database connection
?>
