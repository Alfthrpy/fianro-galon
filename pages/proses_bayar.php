<?php
require_once __DIR__ . '/../config/db.php';

$id_transaksi = $_POST['id_transaksi'];
$metode_bayar = $_POST['pembayaran'];
$dibayar = (int) $_POST['dibayar'];

// Ambil data transaksi berdasarkan ID
$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE id = $id_transaksi");
$data = mysqli_fetch_assoc($query);

$nama_customer = $data['nama_customer'];
$hutang_lama = $data['hutang'];
$dibayar_lama = $data['dibayar'];
$volume_pembelian = $data['volume'];

$sisa_hutang = $hutang_lama - $dibayar;
$dibayar_total = $dibayar_lama + $dibayar;

// Validasi jika dibayar lebih besar dari hutang
if ($dibayar > $hutang_lama) {
    die("Error: Jumlah dibayar melebihi hutang.");
}

// Tambahkan ke kupon jika hutang lunas setelah dibayar
$kupon_baru = 0;
if ($sisa_hutang <= 0) {
    $kupon_baru = $volume_pembelian; // 1 kupon per volume
}

// Update data di tabel transaksi
mysqli_query($conn, "UPDATE transaksi 
    SET dibayar = '$dibayar_total', hutang = '$sisa_hutang', kupon = kupon
    WHERE id = $id_transaksi");

// Update data di tabel pelanggan
mysqli_query($conn, "UPDATE pelanggan 
    SET dibayar = dibayar + $dibayar, hutang = hutang - $dibayar, kupon = kupon
    WHERE nama_customer = '" . mysqli_real_escape_string($conn, $nama_customer) . "'");

header("Location: ../index.php?page=input_order");
exit;
?>
