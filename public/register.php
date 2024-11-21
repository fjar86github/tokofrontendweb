<?php
// Mulai sesi
session_start();

// Cek apakah user sudah login
if (isset($_SESSION['username'])) {
    header('Location: index.php');  // Redirect ke index.php jika sudah login
    exit();
}

$error_message = ''; // Default empty error message

?>

<?php
// Cek apakah file cache ada dan sudah lebih dari X detik (misal 1 jam)
$cacheFile = 'cache/register.html';
$cacheLifeTime = 3600; // Cache akan valid selama 1 jam

if (!file_exists($cacheFile) || (time() - filemtime($cacheFile)) > $cacheLifeTime) {
    // Mulai output buffering untuk menangkap output HTML
    ob_start();

    // Render halaman index.php seperti biasa
    // Semua kode PHP di index.php akan dieksekusi di sini
    include('register.php');

    // Ambil konten HTML yang sudah di-render
    $htmlContent = ob_get_contents();

    // Simpan ke dalam file HTML (sebagai cache)
    file_put_contents($cacheFile, $htmlContent);

    // Kirim output ke browser
    ob_end_flush();
} else {
    // Jika file cache masih valid, langsung tampilkan file cache
    readfile($cacheFile);
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f3f4f6;
            font-family: 'Arial', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background-color: #fff;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        .login-box h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .login-box input[type="text"],
        .login-box input[type="password"],
        .login-box input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .login-box input[type="text"]:focus,
        .login-box input[type="password"]:focus,
        .login-box input[type="email"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-box button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: #f44336;
            margin-top: 10px;
            font-size: 14px;
        }

        .register-link {
            margin-top: 20px;
            font-size: 14px;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 30px;
            }

            .login-box h2 {
                font-size: 20px;
            }

            .login-box button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Daftar</h2>

        <form id="register-form" method="POST" onsubmit="return submitForm(event)">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Daftar</button>

            <div id="error-message" class="error-message"></div>
        </form>

        <div class="register-link">
            Sudah punya akun? <a href="login.php">Login Sekarang</a>
        </div>
    </div>

    <script>
        // Fungsi untuk menangani form submit menggunakan AJAX
        function submitForm(event) {
            event.preventDefault();  // Mencegah form untuk submit secara default

            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            const data = {
                username: username,
                email: email,
                password: password
            };

            // Menggunakan fetch API untuk mengirim data ke backend
            fetch('http://localhost/online_store/api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                // Cek status response dari API
                if (data.status === 'success') {
                    window.location.href = 'login.php';  // Redirect ke login jika registrasi berhasil
                } else {
                    // Tampilkan pesan error jika ada masalah
                    document.getElementById('error-message').innerText = data.message;
                }
            })
            .catch(error => {
                // Menangani error dalam pengiriman request
                document.getElementById('error-message').innerText = 'Terjadi kesalahan, coba lagi.';
            });
        }
    </script>
</body>
</html>
