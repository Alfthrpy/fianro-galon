<?php
require_once __DIR__ . '/../config/db.php';

$id = $_POST['id'];
$nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
$jenis_penukaran = 500;
$jumlah_kupon = $_POST['jumlah'];
$nama = $nama_customer . " " . $jenis_penukaran;


$dibayar = -($jumlah_kupon * $nilai_per_kupon);

$trkupon = -($jumlah_kupon);
// Simpan ke tabel transaksi
$tanggal = date('Y-m-d');
mysqli_query($conn, "INSERT INTO transaksi (tanggal, nama_customer, pembayaran, dibayar, hutang, kupon) 
VALUES ('$tanggal', '$nama', 'kupon', '$dibayar', '0', '$trkupon')");


$result = mysqli_query($conn, "UPDATE `pelanggan` SET `kupon` = `kupon` - $jumlah_kupon WHERE `pelanggan`.`id` = '$id'");

header("Location: ../index.php?page=transaksi");
exit;


?>
