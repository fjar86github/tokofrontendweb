<?php
session_start();
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

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

$title = "Toko Online - Halaman Produk";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$userIcon = $username ? '<i class="fas fa-user"></i>' : '';

// Set the current page and number of items per page
$itemsPerPage = 10;
$totalProducts = count($products);
$totalPages = ceil($totalProducts / $itemsPerPage);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startIndex = ($page - 1) * $itemsPerPage;

// Slice products array based on the current page
$currentProducts = array_slice($products, $startIndex, $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/app.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        <h1>Selamat Datang Di Toko Online Kami</h1>
        <h2>Produk Terbaru</h2>
        
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

        <div id="product-list" class="product-list">
        <?php if (!empty($currentProducts)): ?>
            <?php foreach ($currentProducts as $product): ?>
                <div class="product-item" data-id="<?php echo htmlspecialchars($product['id']); ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>">
                    <p>Produk ID: <?php echo htmlspecialchars($product['id']); ?></p> <!-- Menambahkan ID Produk -->
                    <img src="<?php echo isset($product['image']) ? htmlspecialchars($product['image']) : '../assets/images/default-product.svg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo isset($product['price']) ? number_format((float) $product['price'], 0, ',', '.') : 'Harga tidak tersedia'; ?> IDR</p>

                    <?php if ($isAdmin): ?>
                        <button class="edit-product" data-id="<?php echo htmlspecialchars($product['id']); ?>">Edit</button>
                        <button class="delete-product" data-id="<?php echo htmlspecialchars($product['id']); ?>">Delete</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Produk tidak tersedia.</p>
        <?php endif; ?>
        </div>

        <!-- Pagination Controls -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="prev">Sebelumnya</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="next">Berikutnya</a>
            <?php endif; ?>
        </div>

        <!-- Form Tambah Produk (untuk Admin) -->
        <?php if ($isAdmin): ?>
            <h2>Tambah Produk Baru</h2>
            <form id="add-product-form" class="product-form">
                <div class="form-group">
                    <label for="product-name">Nama Produk</label>
                    <input type="text" id="product-name" placeholder="Nama Produk" required>
                </div>

                <div class="form-group">
                    <label for="product-price">Harga</label>
                    <input type="number" id="product-price" placeholder="Harga" required>
                </div>

                <div class="form-group">
                    <label for="product-stock">Jumlah Stok</label>
                    <input type="number" id="product-stock" placeholder="Jumlah Stok" required>
                </div>

                <div class="form-group">
                    <label for="product-image">URL Gambar</label>
                    <input type="text" id="product-image" placeholder="URL Gambar" required>
                </div>

                <div class="form-group">
                    <label for="product-description">Deskripsi Produk</label>
                    <textarea id="product-description" placeholder="Deskripsi Produk" required></textarea>
                </div>

                <button type="submit" class="btn-submit">Tambah Produk</button>
            </form>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Toko Online. All rights reserved.</p>
    </div>
</footer>

<script>
<?php if ($isAdmin): ?>
    const apiUrl = "http://localhost/online_store/api/";

    // Event listener for "Add Product" form submission
    document.getElementById('add-product-form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Collect form data
        const name = document.getElementById('product-name').value;
        const price = document.getElementById('product-price').value;
        const stock = document.getElementById('product-stock').value;
        const image = document.getElementById('product-image').value;
        const description = document.getElementById('product-description').value;

        // Check if required fields are not empty
        if (!name || !price || !stock || !image || !description) {
            alert("All fields are required.");
            return;
        }

        // Prepare data to send
        const data = {
            name: name,
            price: price,
            stock: stock,
            image: image,
            description: description
        };

        // Sending data via POST to API
        fetch(apiUrl + 'add_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.message === "Product added successfully") {
                alert('Produk berhasil ditambahkan!');
                window.location.reload(); // Reload the page to reflect the new product
            } else {
                alert('Gagal menambahkan produk: ' + (result.message || 'Error tidak diketahui'));
            }
        })
        .catch(error => alert('Terjadi kesalahan: ' + error));
    });

    document.querySelectorAll('.edit-product').forEach(button => {
    button.addEventListener('click', function() {
        const productId = this.getAttribute('data-id');
        const productItem = this.closest('.product-item');
        const productName = productItem.querySelector('h3').innerText;
        const productPrice = productItem.querySelector('p').innerText.replace(' IDR', '').replace(/\./g, '');
        const productDescription = productItem.querySelector('.product-description') ? productItem.querySelector('.product-description').innerText : '';
        const productStock = productItem.querySelector('.product-stock') ? productItem.querySelector('.product-stock').innerText : '';
        const productImage = productItem.querySelector('img').src;

        // Log description to check if it's available
        console.log('Product Description:', productDescription);

        // Prompt user to edit the product details
        const updatedName = prompt("Edit Product Name:", productName);
        const updatedPrice = prompt("Edit Price:", productPrice);
        const updatedDescription = prompt("Edit Description:", productDescription); 
        const updatedStock = prompt("Edit Stock:", productStock);
        const updatedImage = prompt("Edit Image URL:", productImage);

        // Check for invalid inputs from prompt
        if (updatedName === null || updatedPrice === null || updatedDescription === null || updatedStock === null || updatedImage === null) {
            alert("Update cancelled or invalid input.");
            return;
        }

        // Validate price and stock values
        const priceValue = parseFloat(updatedPrice.replace(/[^\d.-]/g, '')); // Remove any non-numeric characters except dot and hyphen
        const stockValue = parseInt(updatedStock, 10); // Parse stock as integer

        // Validate all inputs to ensure they're not empty and price/stock are numbers
        if (updatedName && !isNaN(priceValue) && updatedDescription !== '' && !isNaN(stockValue) && updatedImage) {
            const productData = {
                id: productId,
                name: updatedName,
                price: priceValue,
                description: updatedDescription || '', // Ensure description is always sent as an empty string if it's empty
                stock: stockValue,
                image: updatedImage
            };

            // Log data that will be sent to the backend
            console.log('Product Data to send:', productData);

            // Send update request to the API
            fetch('http://localhost/online_store/api/update_productv2.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(productData)
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Log the response to see what it looks like

                if (data.message === "Product updated successfully") {
                    alert("Produk berhasil diperbarui!");
                   //refresh halaman
                    window.Reload();
                } else {
                    console.error('Update failed with response:', data); // Log the response for debugging
                    alert("Gagal memperbarui produk. Pastikan semua data valid.");
                }
            })
            .catch(error => {
                console.error("Error updating product:", error);
                alert("Terjadi kesalahan saat memperbarui produk.");
            });
        } else {
            alert("Harap periksa kembali input Anda, pastikan semua kolom terisi dengan benar.");
        }
    });
});

    // Event listener untuk tombol "Delete" produk
    document.querySelectorAll('.delete-product').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            if (confirm('Anda yakin ingin menghapus produk ini?')) {
                fetch(apiUrl + `delete_product.php?id=${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Produk berhasil dihapus!');
                        window.location.reload(); // Reload to reflect the deletion
                    } else {
                        alert(result.message);
                        window.location.reload(); // Reload to reflect the deletion
                    }
                })
                .catch(error => alert('Terjadi kesalahan: ' + error));
            }
        });
    });
<?php endif; ?>
</script>

</body>
</html>
