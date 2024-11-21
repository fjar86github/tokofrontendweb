<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session

// Ambil order_id dari URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_id) {
    echo 'Order ID tidak ditemukan.';
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
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
                    <a href="<?php echo isset($_SESSION['username']) ? '/toko/public/logout.php' : '/toko/public/login.php'; ?>" id="login-logout-link">
                        <?php echo isset($_SESSION['username']) ? '<i class="fas fa-user"></i> ' . htmlspecialchars($_SESSION['username']) . ' (Logout)' : 'Masuk'; ?>
                    </a>
                </li>
                <li><a href="register.php">Daftar</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <h1>Detail Pesanan</h1>
        <div id="order-detail">
            <!-- Detail pesanan akan ditampilkan di sini -->
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Toko Online. Semua hak dilindungi.</p>
    </div>
</footer>

<script>
async function fetchOrderDetail() {
    try {
        // Mendapatkan user_id dan order_id dari PHP
        const userId = <?php echo json_encode($user_id); ?>;
        const orderId = <?php echo json_encode($order_id); ?>;

        const response = await fetch('http://localhost/online_store/api/order_details.php?user_id=' + userId + '&order_id=' + orderId, {
            method: 'GET',
        });

        if (!response.ok) {
            throw new Error('Terjadi kesalahan dalam mengambil detail pesanan.');
        }

        const data = await response.json();

        if (data.status === 'error') {
            document.getElementById('order-detail').innerHTML = '<p>' + data.message + '</p>';
            return;
        }

        // Pastikan data memiliki properti yang sesuai
        if (data.data && Array.isArray(data.data) && data.data.length > 0) {
            const orderDetailElement = document.getElementById('order-detail');
            
            // Menampilkan nomor pesanan dan tanggal pesanan
            let orderDetailsHTML = `
                <div class="order-summary">
                    <strong>Nomor Pesanan:</strong> ${data.data[0].order_id} <br>
                    <strong>Tanggal Pesanan:</strong> ${new Date(data.data[0].order_date).toLocaleDateString()} <br>
                </div>
            `;

            let totalPrice = 0;
            orderDetailsHTML += '<h3>Produk dalam Pesanan:</h3>';

            // Menampilkan detail setiap produk dalam pesanan
            data.data.forEach(item => {
                totalPrice += item.total_price; // Menambahkan harga total produk
                orderDetailsHTML += `
                    <div class="order-item">
                        <strong>Product ID:</strong> ${item.product_id} <br>
                        <strong>Nama Product:</strong> ${item.product_name} <br>
                        <strong>Harga:</strong> Rp. ${item.product_price} <br>
                        <strong>Jumlah:</strong> ${item.quantity} <br>
                        <strong>Total Harga Produk:</strong> Rp. ${item.total_price} <br>
                        <hr>
                    </div>
                `;
            });

            // Menampilkan total harga dari semua produk
            orderDetailsHTML += `<strong>Total Harga Pesanan:</strong> Rp. ${totalPrice} <br>`;
            
            // Menampilkan detail pesanan
            orderDetailElement.innerHTML = orderDetailsHTML;
        } else {
            document.getElementById('order-detail').innerHTML = '<p>Data pesanan tidak lengkap.</p>';
        }
    } catch (error) {
        console.error(error);
        document.getElementById('order-detail').innerHTML = '<p>Terjadi kesalahan dalam memuat detail pesanan.</p>';
    }
}

window.onload = fetchOrderDetail;
</script>

</body>
</html>
