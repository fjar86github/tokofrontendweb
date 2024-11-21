<?php
// Mulai sesi untuk mengakses data sesi
session_start();

// Hapus semua sesi yang ada
session_unset();

// Hancurkan sesi
session_destroy();

// Menambahkan script JavaScript untuk menghapus data dari localStorage
echo "<script>
        // Menghapus data pengguna dari localStorage
        localStorage.removeItem('user');
        
        // Arahkan pengguna ke halaman login setelah logout
        window.location.href = 'login.php'; 
      </script>";
?>
