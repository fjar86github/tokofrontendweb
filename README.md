# Toko Online Front-End Web

Ini adalah proyek toko online dengan tampilan depan (front-end) yang modern dan cantik, dirancang untuk memberikan pengalaman berbelanja yang lancar. Dibangun menggunakan HTML, CSS, dan JavaScript, proyek ini terintegrasi dengan API backend dan menggunakan service worker untuk meningkatkan kinerja serta memungkinkan penggunaan offline.

## Fitur

- **Desain Responsif**: Toko online ini dirancang untuk tampil sempurna di berbagai perangkat, seperti desktop, tablet, dan ponsel pintar.
- **Autentikasi Pengguna**: Pengguna dapat mendaftar, masuk, dan mengelola akun mereka dengan aman, memungkinkan pengalaman berbelanja yang lebih personal serta pelacakan riwayat pesanan.
- **Katalog Produk**: Toko menampilkan produk dengan detail seperti nama, gambar, dan harga. Produk dapat difilter atau dicari menggunakan bilah pencarian yang intuitif.
- **Keranjang Belanja**: Pengguna dapat menambahkan produk ke keranjang belanja dan melihat barang-barang yang ada di keranjang sebelum melanjutkan ke proses checkout.
- **Manajemen Pesanan**: Pengguna dapat melakukan pemesanan dan melihat riwayat pesanan dengan informasi detail.
- **Dukungan Offline**: Dengan bantuan service worker, toko tetap dapat berfungsi meskipun pengguna sedang offline, memastikan pengalaman berbelanja yang mulus kapan saja.
## Prasyarat

- PHP 5.3 atau lebih tinggi
- MySQL/MariaDB 5.1 atau lebih tinggi
- Apache Server dengan mod_rewrite

## Instalasi

1. **Clone atau Unduh Proyek**:
   Anda dapat meng-clone atau mengunduh repositori ini ke dalam direktori server Anda.

2. **Pengaturan Database**:
   - Buat database di MySQL: `online_store_db`.
   - Jalankan skrip SQL untuk membuat tabel yang diperlukan: `users`, `products`, `orders`.

3. **Konfigurasi File**:
   - Edit file `config.php` untuk mengatur koneksi database dan pengaturan lainnya.

4. **Install Dependensi (Opsional)**:
   Jika Anda menggunakan Composer, jalankan perintah berikut untuk menginstal dependensi yang diperlukan:
   ```bash
   composer install
## Struktur Proyek

### `C:`
Direktori utama berisi file penting proyek:
- `composer.json`: Berisi dependensi PHP untuk backend.
- `manifest.json`: Manifest aplikasi web untuk konfigurasi PWA.
- `online_store_db.sql`: File SQL untuk membuat dan mengisi database.
- `README.md`: File ini.

### `assets/`
Berisi file statis seperti gambar, CSS, dan JavaScript yang digunakan untuk styling dan elemen interaktif:
- **`css/`**: 
  - `style.css`: File stylesheet utama untuk desain antarmuka toko online.
- **`images/`**: 
  - `default-product.svg`: Gambar default untuk produk tanpa gambar yang ditentukan.
  - `icon-192.svg`, `icon-512.svg`: Ikon untuk aplikasi web yang digunakan dalam berbagai ukuran pada perangkat.
  - `logo.svg`: Logo toko online.
- **`js/`**: 
  - `app.js`: File JavaScript yang berisi logika utama untuk fitur front-end toko online seperti penanganan keranjang belanja, tampilan produk, dan pencarian produk.

### `backend/`
Berisi file PHP untuk menangani permintaan API yang terkait dengan operasi backend toko:
- **Endpoint API**: 
  - `add_product.php`, `delete_product.php`, `update_product.php`: Menangani penambahan, penghapusan, dan pembaruan produk.
  - `get_product.php`, `get_products.php`: Mengambil produk tunggal atau daftar produk.
  - `get_search.php`: Mencari produk berdasarkan nama atau kategori.
  - `order.php`, `orderv2.php`, `order_details.php`: Menangani pembuatan dan detail pesanan.
  - `register.php`, `login.php`, `logout.php`: Mengelola autentikasi pengguna.
  - `forgot_password.php`: Fitur pemulihan kata sandi.
  - `sales_statistic.php`: Mengumpulkan data penjualan untuk pelaporan.
  - `import.php`, `export.php`: Mengimpor dan mengekspor data produk.
  
### `public/`
Halaman publik yang dapat diakses pengguna di toko online:
- `index.php`: Halaman utama yang menampilkan produk-produk unggulan dan promo.
- `cart.php`: Halaman keranjang belanja, tempat pengguna dapat melihat dan mengelola barang-barang yang ada di keranjang.
- `checkout.php`: Halaman checkout tempat pengguna dapat menyelesaikan pesanan mereka.
- `order_history.php`: Menampilkan daftar pesanan sebelumnya yang dilakukan oleh pengguna.
- `order_detail.php`: Menyediakan informasi detail untuk pesanan tertentu.
- `products.php`: Halaman produk di mana pengguna dapat menelusuri produk yang tersedia.
- `login.php`, `register.php`: Halaman login dan pendaftaran pengguna.
- `offline.html`: Halaman cadangan yang ditampilkan ketika pengguna sedang offline.

### `service-worker/`
Berisi file service worker:
- **`service-worker.js`**: File JavaScript yang mengaktifkan caching sumber daya untuk penggunaan offline dan memastikan toko tetap berfungsi meskipun jaringan tidak tersedia.

## Cara Menjalankan

1. **Clone repositori ini ke mesin lokal Anda:**
   ```bash
   git clone https://github.com/yourusername/online-store.git
   cd online-store
Siapkan backend:

Impor skema database (online_store_db.sql) ke database MySQL atau MariaDB Anda.
Pastikan server Anda berjalan dengan PHP dan file-file backend terkonfigurasi untuk berkomunikasi dengan database.
Install dependensi yang dibutuhkan menggunakan Composer:

bash
Copy code
composer install
Jalankan proyek:

Anda dapat menggunakan server PHP lokal atau mengonfigurasi server langsung untuk mengakses toko.
Setelah semuanya siap, buka index.php di browser Anda.
Kontribusi
Kontribusi sangat diterima! Silakan fork repositori ini, buat perubahan, dan kirim pull request.

Lisensi
Proyek ini bersifat open-source dan tersedia di bawah Lisensi MIT.


---
### Screenshoot Front End Web
Halaman index.php yang dimuat sebelum login (Tersedia tambah keranjang, checkout setelah anda login dan paging, pencarian berbasis ajax DOM)
![image](https://github.com/user-attachments/assets/e6fa4d6d-695f-449f-8f22-d04901f84bc6)
Halaman Login
![image](https://github.com/user-attachments/assets/99cc3851-9c7a-4594-9e76-8ac7455889e1)
Tambah Keranjang setelah login berhasil
![image](https://github.com/user-attachments/assets/4727487b-d4e7-44e4-acf7-e6b9d11418ba)
proses checkout
![image](https://github.com/user-attachments/assets/f589765c-8396-4682-a98a-1d5fed4d0fbb)
riwayat pesanan setelah user login
![image](https://github.com/user-attachments/assets/5787217c-c2c1-45f7-addf-1ef397286b7b)
detail pesanan setelah user login
![image](https://github.com/user-attachments/assets/aaed818f-349e-4585-964d-a2032da89660)
pemeriksaan keranjang, jika user sudah login
![image](https://github.com/user-attachments/assets/fb02550b-0c3c-485e-9ae9-7679748a2a73)
Manajemen produk, tambah, edit, update dan delete setelah user login
![image](https://github.com/user-attachments/assets/224c1326-ff88-404e-915c-cf66709103dc)
![image](https://github.com/user-attachments/assets/8e03f703-1470-4991-8a42-ed9cecbedaeb)
Register untuk menambahkan user
![image](https://github.com/user-attachments/assets/37b21beb-ce9b-4e3c-97c6-2b43b5bfedc4)
Ketersediaan Layanan Offline web
![image](https://github.com/user-attachments/assets/775c0402-3dd0-48cf-88fc-52a9a2f9776a)
![image](https://github.com/user-attachments/assets/2c79214a-9c85-4db1-8530-c7d44bc33a6d)






