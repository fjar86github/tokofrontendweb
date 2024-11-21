<?php
session_start();

// Pastikan pengguna sudah login (session)
if (!isset($_SESSION['username'])) {
    header('Location: login.php');  // Redirect ke halaman login jika belum login
    exit();
}

// Mendapatkan user_id dan username dari session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Mengatur title halaman
$title = "Riwayat Pesanan";
$userIcon = '<i class="fas fa-user"></i>';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<header>
    <div class="container">
        <img src="../assets/images/logo.svg" alt="Toko Online Logo" id="logo">
        <nav>
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="products.php">Produk</a></li>
                <li><a href="cart.php">Keranjang</a></li>
                <li><a href="order_history.php">Riwayat Pesanan</a></li>
                <li>
                    <a href="<?php echo $username ? '/toko/public/logout.php' : '/toko/public/login.php'; ?>" id="login-logout-link">
                        <?php echo $username ? $userIcon . ' ' . htmlspecialchars($username) . ' (Logout)' : 'Masuk'; ?>
                    </a>
                </li>
                <li><a href="register.php">Daftar</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <h1>Riwayat Pesanan</h1>
        <div id="order-list">
            <!-- Riwayat pesanan akan ditampilkan di sini -->
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Toko Online. Semua hak dilindungi.</p>
    </div>
</footer>

<script>
    async function fetchOrderHistory() {
        try {
            // Mendapatkan user_id yang disisipkan dengan PHP
            const userId = <?php echo $user_id; ?>;

            // Membuat URL dengan user_id sebagai parameter query
            const response = await fetch('http://localhost/online_store/api/orderv2.php?user_id=' + userId);

            if (!response.ok) {
                throw new Error('Terjadi kesalahan dalam mengambil data pesanan.');
            }

            const orders = await response.json();
            const orderListElement = document.getElementById('order-list');
            if (orders.status === 'error' || orders.length === 0) {
                orderListElement.innerHTML = '<p style="text-align: center;">Anda belum memiliki pesanan.</p>';
                return;
            }

            let htmlContent = '<ul>';
            orders.forEach(order => {
                htmlContent += `
                    <li>
                        <strong>Nomor Pesanan:</strong> ${order.id} <br>
                        <strong>Produk Id:</strong> ${order.product_id} <br>
                        <strong>Jumlah:</strong> ${order.quantity} <br>
                        <strong>Tanggal:</strong> ${order.order_date} <br>
                        <a href="order_detail.php?user_id=<?php echo $user_id; ?>&order_id=${order.id}">Lihat Detail</a>
                    </li>
                `;
            });
            htmlContent += '</ul>';
            orderListElement.innerHTML = htmlContent;
        } catch (error) {
            console.error(error);
            document.getElementById('order-list').innerHTML = '<p>Terjadi kesalahan dalam memuat riwayat pesanan.</p>';
        }
    }

    // Memanggil fungsi untuk fetch data pesanan ketika halaman dimuat
    window.onload = fetchOrderHistory;
</script>


</body>
</html>
