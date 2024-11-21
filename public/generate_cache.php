<?php
// Pastikan folder cache ada, jika tidak, buat folder cache
if (!file_exists('cache')) {
    mkdir('cache', 0777, true); // Membuat folder cache jika belum ada
    echo "Folder cache dibuat.\n"; // Menampilkan pesan jika folder berhasil dibuat
} else {
    echo "Folder cache sudah ada.\n"; // Menampilkan pesan jika folder sudah ada
}

// Daftar file PHP yang akan di-render menjadi HTML
$files = [
    'index.php',
    'cart.php',
    'login.php',
    'logout.php',
    'order_detail.php',
    'order_history.php',
    'products.php',
    'register.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        // Mulai output buffering untuk menangkap konten
        ob_start();
        
        include($file); // Sertakan file PHP
        
        $htmlContent = ob_get_clean(); // Ambil konten HTML dan hentikan buffering
        
        // Simpan konten ke file HTML di folder cache
        $outputFile = 'cache/' . basename($file, '.php') . '.html';
        if (file_put_contents($outputFile, $htmlContent)) {
            echo "File $outputFile berhasil dibuat.\n"; // Pesan jika file berhasil dibuat
        } else {
            echo "Gagal menyimpan file $outputFile.\n"; // Pesan jika gagal menyimpan
        }
    } else {
        echo "File $file tidak ditemukan.\n"; // Pesan jika file tidak ditemukan
    }
}
?>
