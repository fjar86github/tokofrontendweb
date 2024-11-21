<?php
// Menggunakan path relatif untuk mengimpor config.php dari direktori yang lebih atas
$config = require 'config.php';

// Mengambil pengaturan database
$servername = $config['db']['host'];
$username = $config['db']['username'];
$password = $config['db']['password'];
$dbname = $config['db']['dbname'];
$apiEndpoint = $config['api']['endpoint'];



//$servername = "localhost";
//$username = "root"; // or your database username
//$password = ""; // or your database password
//$dbname = "online_store_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
