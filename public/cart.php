<?php
// Memulai session untuk mengecek login
session_start();

// URL API yang ingin diakses
$url = "http://localhost/online_store/api/get_products.php";

// Inisialisasi CURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Eksekusi CURL dan ambil responsnya
$response = curl_exec($ch);
if ($response === false) {
    echo "Error: Gagal mengakses API. " . curl_error($ch);
    exit;
}

// Tutup CURL
curl_close($ch);

// Decode JSON menjadi array asosiatif PHP
$data = json_decode($response, true);
$products = $data !== null ? $data : [];

// Konfigurasi paginasi
$itemsPerPage = 10; // Jumlah produk per halaman
$totalProducts = count($products); // Total produk
$totalPages = ceil($totalProducts / $itemsPerPage); // Total halaman

// Mendapatkan halaman saat ini
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($totalPages, $page));

// Menentukan indeks awal dan akhir produk yang akan ditampilkan
$startIndex = ($page - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage, $totalProducts);

// Mengatur title halaman
$title = "Toko Online - Halaman Keranjang Belanja";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$userIcon = $username ? '<i class="fas fa-user"></i>' : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../assets/js/app.js" defer></script>
</head>
<body>
<!-- User ID disimpan di elemen HTML -->
<div id="user-data" data-user-id="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>"></div>
<!-- Script untuk mengambil data user_id dan menggunakannya di app.js -->
<script>
        // Menyisipkan data PHP ke dalam JavaScript
        const userId = document.getElementById('user-data').getAttribute('data-user-id');
        console.log(userId);  // Untuk memeriksa nilai userId
    </script>
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
        <h1>Selamat Datang di Toko Online Kami!</h1>
        

        <h2>Keranjang Belanja</h2>
        <div id="cart-items"></div>
        
        <?php if ($username): ?>
    <div id="checkout-form">
        <button class="add-to-cart" id="checkout-button" onclick="checkout()">Proses Checkout</button>
    </div>
<?php else: ?>
    <p><a href="/toko/public/login.php">Masuk untuk melanjutkan ke checkout</a></p>
<?php endif; ?>

    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Toko Online. Semua hak dilindungi.</p>
    </div>
</footer>

<script>
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        addToCart(this.getAttribute('data-id'));
    });
});

function addToCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const index = cart.findIndex(item => item.id === productId);
    if (index !== -1) {
        cart[index].quantity += 1;
    } else {
        cart.push({ id: productId, quantity: 1 });
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

function removeFromCart(productId) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart = cart.filter(item => item.id !== productId); // Hapus item dari keranjang
    localStorage.setItem('cart', JSON.stringify(cart)); // Update localStorage
    updateCartDisplay(); // Perbarui tampilan keranjang
}

function updateCartDisplay() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const cartItemsContainer = document.getElementById('cart-items');
    cartItemsContainer.innerHTML = '';
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p>Keranjang Anda kosong.</p>';
    } else {
        cart.forEach(item => {
            const product = <?php echo json_encode($products); ?>.find(p => p.id == item.id);
            if (product) {
                cartItemsContainer.innerHTML += `
                    <div class="cart-item">
                        <h4>${product.name}</h4>
                        <p>Jumlah: ${item.quantity}</p>
                        <button class="delete-product" onclick="removeFromCart('${item.id}')">Hapus</button>
                    </div>
                `;
            }
        });
    }
}

// Memperbarui tampilan keranjang saat halaman dimuat
updateCartDisplay();
</script>

</body>
</html>
