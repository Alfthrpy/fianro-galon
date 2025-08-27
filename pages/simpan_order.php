<?php
require_once __DIR__ . '/../config/db.php';

// Ambil data dari form
$tipe_customer = $_POST['tipe_customer'];
$nama_customer = '';
$pembayaran = $_POST['pembayaran'];
$simpan_customer_baru = isset($_POST['simpan_customer_baru']) ? 1 : 0;

if ($tipe_customer === 'lama') {
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer_lama']);
} else {
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer_baru']);
}

$dibayar = 0;
$volume = (int) $_POST['volume_pembelian'];



// Ambil tipe pembelian dari form
$tipe_pembelian = $_POST['tipe_pembelian']; 

// Tentukan harga per liter berdasarkan tipe pembelian
if ($tipe_pembelian === 'massal') {
    $harga_per_liter = 4000; // Harga untuk reseller
} else {
    $harga_per_liter = 5000; // Harga default untuk perorangan
}

// Hitung kupon dan hutang berdasarkan volume dan harga yang sudah ditentukan
$kupon = $volume;
$kup = 0;
// Baris ini diganti dari yang sebelumnya
$hutang = $volume * $harga_per_liter; 

// =============================================== -->

$tanggal = date('Y-m-d');

// Simpan ke tabel transaksi
$queryTransaksi = "INSERT INTO `transaksi` 
                   (`tanggal`,`nama_customer`, `volume`, `pembayaran`, `dibayar`, `hutang`, `kupon`)
                   VALUES ('$tanggal', '$nama_customer', '$volume', '$pembayaran', '$dibayar', '$hutang', '$kup')";
mysqli_query($conn, $queryTransaksi);

// Cek apakah pelanggan sudah ada
$cek = mysqli_query($conn, "SELECT * FROM pelanggan WHERE nama_customer = '$nama_customer'");

if (mysqli_num_rows($cek) > 0) {
    // ✅ Customer lama -> UPDATE
    mysqli_query($conn, "UPDATE pelanggan 
                         SET dibayar = dibayar + $dibayar,
                             hutang = hutang + $hutang,
                             kupon = kupon + $kupon
                         WHERE nama_customer = '$nama_customer'");
} else if ($tipe_customer === 'baru' && $simpan_customer_baru) {
    // ✅ Customer baru -> INSERT
    $queryPelanggan = "INSERT INTO `pelanggan` 
                           (`nama_customer`, `dibayar`, `hutang`, `kupon`) 
                           VALUES ('$nama_customer', '$dibayar', '$hutang', '$kupon')";
    mysqli_query($conn, $queryPelanggan);
}

// Redirect kembali ke halaman transaksi
header("Location: ../index.php?page=input_order");
exit;
?>