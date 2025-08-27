<?php

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    // Ambil data transaksi yang akan dihapus
    $sql = "SELECT nama_customer, dibayar, hutang FROM transaksi WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $transaksi = mysqli_fetch_assoc($result);

    if ($transaksi) {
        $nama_customer = $transaksi['nama_customer'];
        $dibayar = $transaksi['dibayar'];
        $hutang = $transaksi['hutang'];

        // Kurangi saldo pelanggan
        $sql_update = "UPDATE pelanggan 
                       SET dibayar = dibayar - $dibayar,
                           hutang = hutang - $hutang
                       WHERE nama_customer = '".mysqli_real_escape_string($conn, $nama_customer)."'";
        mysqli_query($conn, $sql_update);

        // Hapus transaksi
        $sql_delete = "DELETE FROM transaksi WHERE id = $id";
        mysqli_query($conn, $sql_delete);
    }
}

header("Location: ../index.php?page=input_order");
exit();
