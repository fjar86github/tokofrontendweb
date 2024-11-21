<?php
// register.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
include('../db.php');
//$host = 'localhost';
//$db = 'online_store_db';
//$user = 'root';
//$pass = '';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]);
    exit();
}

$data = json_decode(file_get_contents("php://input"));

if (isset($data->username) && isset($data->password) && isset($data->email)) {
    $username = $data->username;
    $password = password_hash($data->password, PASSWORD_BCRYPT);
    $email = $data->email;

    $checkQuery = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $checkQuery->bindParam(':email', $email);
    $checkQuery->execute();

    if ($checkQuery->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email already registered"]);
    } else {
        $query = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
        $query->bindParam(':username', $username);
        $query->bindParam(':password', $password);
        $query->bindParam(':email', $email);

        if ($query->execute()) {
            echo json_encode(["status" => "success", "message" => "Registration successful"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Registration failed"]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>
