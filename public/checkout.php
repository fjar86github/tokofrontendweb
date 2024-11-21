<?php
// Memasukkan konfigurasi dasar
require_once('../backend/config/db.php');

// Mengatur title halaman
$title = "Toko Online - Checkout";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/app.js" defer></script>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="container">
            <img src="../assets/images/logo.png" alt="Toko Online Logo" id="logo">
            <nav>
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="products.php">Produk</a></li>
                    <li><a href="cart.php">Keranjang</a></li>
                    <li><a href="order_history.php">Riwayat Pesanan</a></li>
                    <li><a href="login.php">Masuk</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="container">
            <h1>Checkout</h1>

            <!-- Menampilkan rincian pesanan -->
            <div id="checkout-items">
                <!-- Rincian pesanan akan dimuat disini oleh app.js -->
            </div>

            <h3>Total Pembayaran: Rp <span id="total-price">0</span></h3>

            <!-- Formulir Pembayaran -->
            <form id="checkout-form">
                <label for="address">Alamat Pengiriman:</label>
                <textarea id="address" name="address" required></textarea>
                <label for="payment-method">Metode Pembayaran:</label>
                <select id="payment-method" name="payment-method" required>
                    <option value="credit-card">Kartu Kredit</option>
                    <option value="bank-transfer">Transfer Bank</option>
                    <option value="cash-on-delivery">Bayar di Tempat</option>
                </select>
                <button type="submit" id="submit-button">Selesaikan Pembayaran</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Toko Online. Semua hak dilindungi.</p>
        </div>
    </footer>

</body>
</html>
