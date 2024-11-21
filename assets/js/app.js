// app.js

// Registrasi Service Worker jika browser mendukung
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/toko/service-worker/service-worker.js')
            .then((registration) => {
                console.log('Service Worker terdaftar:', registration);

                // Registrasi Background Sync
                if ('SyncManager' in window) {
                    navigator.serviceWorker.ready.then((registration) => {
                        const pendingOrders = JSON.parse(localStorage.getItem('pending-orders')) || [];
                        if (pendingOrders.length > 0) {
                            registration.sync.register('sync-order')
                                .then(() => console.log('Background Sync terdaftar'))
                                .catch((error) => console.error('Background Sync gagal:', error));
                        }
                    });
                }
            })
            .catch((error) => console.error('Service Worker gagal didaftarkan:', error));
    });
}

let deferredPrompt;

window.addEventListener('beforeinstallprompt', (event) => {
    // Mencegah browser untuk menampilkan dialog default
    console.log("beforeinstallprompt event detected!");  // Tambahkan log ini
    event.preventDefault();
    deferredPrompt = event;

    // Tampilkan tombol atau UI untuk mengundang pengguna menambahkan ke home screen
    const installButton = document.createElement('button');
    installButton.innerText = 'Add to Home Screen';
    document.body.appendChild(installButton);

    installButton.addEventListener('click', () => {
        // Tampilkan prompt untuk instalasi aplikasi
        deferredPrompt.prompt();

        // Tunggu user untuk memilih
        deferredPrompt.userChoice.then((choiceResult) => {
            console.log(choiceResult.outcome); // 'accepted' atau 'dismissed'
            deferredPrompt = null;
        });
    });
});



// Mengambil pesanan yang tertunda dari localStorage
function getPendingOrder() {
    return JSON.parse(localStorage.getItem('pendingOrder'));
}
// Fungsi untuk mendapatkan data keranjang dari localStorage
function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || [];
}

// Ambil user_id dari elemen HTML yang telah disisipkan oleh PHP (jika ada)
//userid sudah ada di indexedDB.php

// Fungsi untuk menyimpan pesanan yang tertunda (offline)
function savePendingOrder(orderData) {
    let pendingOrders = JSON.parse(localStorage.getItem('pending-orders')) || [];
    pendingOrders.push(orderData);
    localStorage.setItem('pending-orders', JSON.stringify(pendingOrders));
}

// Fungsi untuk menghitung total harga keranjang
async function calculateTotalPrice(cart) {
    let totalPrice = 0;
    for (let item of cart) {
        const product = await fetchProductById(item.id);
        totalPrice += product.price * item.quantity;
    }
    return totalPrice;
}

// Fungsi untuk mengambil produk berdasarkan ID
async function fetchProductById(productId) {
    const response = await fetch(`http://localhost/online_store/api/get_product.php?id=${productId}`);
    const data = await response.json();
    return data;  // Produk dengan ID yang diminta
}

async function checkout() {
    const cart = getCart();
    const userIdElement = document.getElementById('userId');
    const userId = userIdElement ? userIdElement.getAttribute('data-user-id') : null;

    if (cart.length === 0) {
        alert('Keranjang Anda kosong. Silakan tambahkan produk terlebih dahulu.');
        return;
    }

    if (!userId) {
        alert('Anda harus login untuk melanjutkan checkout.');
        return;
    }

    try {
        for (let item of cart) {
            const orderData = {
                user_id: userId,
                product_id: item.id,
                quantity: item.quantity,
                order_date: new Date().toISOString().split('T')[0],
            };

            console.log('Order Data (Before Sending):', orderData);

            if (navigator.onLine) {
                const response = await fetch('http://localhost/online_store/api/orderv2.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderData),
                });

                // Periksa apakah status response OK dan pastikan formatnya JSON
                const responseText = await response.text();
                if (!response.ok) {
                    console.error('Error Response:', responseText);
                    alert('Terjadi kesalahan di server: ' + responseText);
                    continue;
                }

                // Debugging: Cek apa yang diterima dari server
                console.log("Response Text:", responseText);

                // Coba parse response sebagai JSON setelah memeriksa apakah itu JSON yang valid
                let data;
                try {
                    data = JSON.parse(responseText); // Parsing teks menjadi JSON
                } catch (error) {
                    console.error('Error parsing JSON:', error);
                    alert('Terjadi kesalahan parsing JSON. Respon server tidak valid.');
                    continue;
                }

                console.log('Response from server:', data);

                if (data.status === 'success') {
                    console.log('Order berhasil:', orderData);
                } else {
                    console.error(`Kesalahan pada server: ${data.message}`);
                    alert(`Gagal memproses pesanan: ${data.message}`);
                }
            } else {
                savePendingOrder(orderData);
                alert('Anda sedang offline, pesanan akan diproses saat koneksi tersedia.');
            }
        }

        // Kosongkan keranjang setelah semua pesanan diproses
        localStorage.removeItem('cart');
        alert('Checkout selesai!');
        window.location.href = 'order_history.php';
    } catch (error) {
        console.error('Terjadi kesalahan:', error);
        alert('Gagal memproses pesanan, coba lagi nanti.');
    }
}




