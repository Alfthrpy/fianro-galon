<?php
session_start();
// Tangkap nama halaman dari parameter URL
$page = $_GET['page'] ?? 'login';
$title = ucfirst($page) . " | Company Profile";

// Tentukan path file konten
$filepath = "pages/{$page}.php";

// Proteksi halaman dashboard: redirect jika belum login
$protected_pages = ['dashboard', 'transaksi','input_order','simpan_order']; // tambah di sini jika ada halaman lain yg butuh login

if (in_array($page, $protected_pages) && !isset($_SESSION['username'])) {
    header('Location: index.php?page=login');
    exit;
}

// Cek apakah file halaman ada
if (file_exists($filepath)) {

    include $filepath;
        
} else {
    // Jika file tidak ditemukan, tampilkan pesan error
    include "base/header.php";
    echo "<div class='container text-center mt-5'><h2>Halaman <code>{$page}</code> tidak ditemukan</h2></div>";
    include "base/footer.php";
}
