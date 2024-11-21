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

// Filter produk berdasarkan pencarian jika ada
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
if ($searchQuery) {
    $products = array_filter($products, function ($product) use ($searchQuery) {
        return strpos(strtolower($product['name']), $searchQuery) !== false ||
               strpos(strtolower($product['category']), $searchQuery) !== false;
    });
}

// Konfigurasi paginasi
$itemsPerPage = 10;
$totalProducts = count($products);
$totalPages = ceil($totalProducts / $itemsPerPage);

// Mendapatkan halaman saat ini
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($totalPages, $page));

// Menentukan indeks awal dan akhir produk yang akan ditampilkan
$startIndex = ($page - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage, $totalProducts);

$title = "Toko Online - Halaman Utama";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$userIcon = $username ? '<i class="fas fa-user"></i>' : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="manifest" href="/toko/manifest.json">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../assets/js/app.js" defer></script>
</head>
<body>
<!-- Menyisipkan user_id ke dalam elemen data-user-id -->
<div id="userId" data-user-id="<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>"></div>

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
        <h1>Selamat Datang di Toko Online Kami</h1>
        
        <!-- Formulir Pencarian -->
        <!-- Input Pencarian -->
        <input type="text" id="search-input" placeholder="Cari produk..." class="search-input">
        <script>
            document.getElementById('search-input').addEventListener('input', function() {
                const searchQuery = this.value.toLowerCase();
                const productItems = document.querySelectorAll('.product-item');

                productItems.forEach(function(item) {
                    const productName = item.getAttribute('data-name').toLowerCase();

                    if (productName.includes(searchQuery)) {
                        item.style.display = '';  // Tampilkan produk jika cocok
                    } else {
                        item.style.display = 'none';  // Sembunyikan produk jika tidak cocok
                    }
                });
            });
        </script>
        <h2>Produk Terbaru</h2>     
        <div id="product-list" class="product-list">
            <?php if (!empty($products)): ?>
                <?php for ($i = $startIndex; $i < $endIndex; $i++): ?>
                    <?php $product = $products[$i]; ?>
                    <?php $productImage = $product['image'] ? $product['image'] : '../assets/images/default-product.svg'; ?>
                    <div class="product-item" data-id="<?php echo htmlspecialchars($product['id']); ?>">
                        <img src="<?php echo htmlspecialchars($productImage); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" />
                        <h3><?php echo htmlspecialchars($product['name']); ?> - Stok: <?php echo htmlspecialchars($product['stock']); ?></h3>
                        <p>ID Produk: <?php echo htmlspecialchars($product['id']); ?></p>
                        <p><?php echo number_format($product['price'], 0, ',', '.'); ?> IDR</p>
                        <?php if ($username): ?>
                            <button class="add-to-cart" data-id="<?php echo htmlspecialchars($product['id']); ?>">Tambah ke Keranjang</button>
                        <?php else: ?>
                            <p><a class="add-to-cart" href="/toko/public/login.php">Masuk untuk menambah ke keranjang</a></p>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            <?php else: ?>
                <div class="no-products-message">
                    <p><strong>Produk tidak tersedia saat ini.</strong></p>
                    <p>Silakan coba lagi nanti atau <a href="#" onclick="alert('Hubungi Kami')">hubungi kami</a> jika ada pertanyaan.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($searchQuery); ?>" class="pagination-button">Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchQuery); ?>" class="pagination-number <?php echo $page === $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($searchQuery); ?>" class="pagination-button">Berikutnya</a>
            <?php endif; ?>
        </div>

        <h2>Keranjang Belanja</h2>
        <div id="cart-items"></div>
        
        <?php if ($username): ?>
    <div id="checkout-form">
        <button id="checkout-button" class="add-to-cart" onclick="checkout()">Proses Checkout</button>
    </div>
<?php else: ?>
    <p><a class="add-to-cart" href="/toko/public/login.php">Masuk untuk melanjutkan ke checkout</a></p>
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
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
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
                    <h4>${product.id}</h4>
                        <h4>${product.name}</h4>
                        <p>Jumlah: ${item.quantity}</p>
                        <button class="delete-product" onclick="removeFromCart('${item.id}')">Hapus</button>
                    </div>
                `;
            }
        });
    }
}

document.querySelector('#search-input').addEventListener('input', function() {
    const searchQuery = this.value.toLowerCase();
    const products = document.querySelectorAll('.product-item');
    
    products.forEach(product => {
        const name = product.querySelector('h3').innerText.toLowerCase();
        const category = product.querySelector('.category') ? product.querySelector('.category').innerText.toLowerCase() : '';
        
        if (name.includes(searchQuery) || category.includes(searchQuery)) {
            product.style.display = '';
        } else {
            product.style.display = 'none';
        }
    });
});


updateCartDisplay();
</script>

</body>
</html>
