<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Handle login when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // API URL for login
    $url = 'http://localhost/online_store/api/login.php';

    // Data to send
    $data = array(
        'username' => $username,
        'password' => $password
    );

    // Encode the data to JSON
    $json_data = json_encode($data);

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response as string
    curl_setopt($ch, CURLOPT_POST, true); // Use POST request
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data); // Set the JSON data in the request body
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json' // Set content-type to JSON
    ));

    // Execute the request and capture the response
    $response = curl_exec($ch);

    // Check if there was an error with the cURL request
    if ($response === false) {
        $error_message = 'Error occurred while making API request. Please try again.';
    } else {
        // Close cURL session
        curl_close($ch);

        // Decode JSON response
        $responseData = json_decode($response, true);

        // Handle response based on message from API
        if (isset($responseData['message'])) {
            if ($responseData['message'] === "Login successful") {
                // Login success, set session variables
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $responseData['user_id'];
                $_SESSION['logged_in'] = true;

                // Determine role based on username
                if ($username === 'admin') {
                    $_SESSION['role'] = 'admin';
                    // Redirect to index dashboard
                    header('Location: index.php');
                } else {
                    $_SESSION['role'] = 'user';
                    // Redirect to user dashboard or index
                    header('Location: index.php');
                }
                exit();
            } else {
                // Display an error message if login fails
                $error_message = isset($responseData['message']) ? $responseData['message'] : 'Login failed, please check your username and password.';
            }
        } else {
            $error_message = 'Unknown error occurred, please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }

        .login-box {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            color: #555;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: #d9534f;
            background-color: #f8d7da;
            padding: 10px;
            margin-top: 15px;
            border-radius: 4px;
            font-size: 14px;
        }

        .register-link {
            margin-top: 15px;
            color: #007bff;
        }

        .register-link a {
            text-decoration: none;
            color: #007bff;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>

        <form id="login-form" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>

            <?php if (isset($error_message)) : ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar Sekarang</a>
        </div>
    </div>
</body>
</html>
