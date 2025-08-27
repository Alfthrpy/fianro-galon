<?php

require_once __DIR__ . '/../config/db.php';

if (isset($_POST['tambah'])) {
    $id     = intval($_POST['id']);
    $jumlah = intval($_POST['jumlah']);

    $conn->query("UPDATE pelanggan SET kupon = kupon + $jumlah WHERE id = $id") or die($conn->error);
    header("Location: ../index.php?page=pelanggan&success=Kupon berhasil ditambah");
    exit;
}

if (isset($_POST['kurangi'])) {
    $id     = intval($_POST['id']);
    $jumlah = intval($_POST['jumlah']);

    // Pastikan kupon tidak negatif
    $conn->query("UPDATE pelanggan SET kupon = GREATEST(kupon - $jumlah, 0) WHERE id = $id") or die($conn->error);
    header("Location: ../index.php?page=pelanggan&success=Kupon berhasil dikurangi");
    exit;
}
?>
