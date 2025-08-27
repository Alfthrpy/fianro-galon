<?php
require_once __DIR__ . '/../config/db.php';

// Supaya error SQL jelas
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Ambil & sanitasi input
    $id_pelanggan = isset($_POST['id_pelanggan']) ? (int)$_POST['id_pelanggan'] : 0;
    $volume       = isset($_POST['volume']) ? (int)$_POST['volume'] : 0;
    $pakai_kupon  = isset($_POST['pakai_kupon']) ? (int)$_POST['pakai_kupon'] : 0;

    if ($id_pelanggan <= 0 || $volume <= 0 || $pakai_kupon < 0) {
        throw new Exception('Data tidak valid');
    }

    // Ambil data pelanggan
    $q = $conn->prepare("SELECT nama_customer, kupon, hutang FROM pelanggan WHERE id = ?");
    $q->bind_param("i", $id_pelanggan);
    $q->execute();
    $pel = $q->get_result()->fetch_assoc();
    if (!$pel) {
        throw new Exception('Pelanggan tidak ditemukan');
    }

    $nama_customer = $pel['nama_customer'];
    $stok_kupon    = (int)$pel['kupon'];

    if ($pakai_kupon > $stok_kupon) {
        throw new Exception('Jumlah kupon yang dipakai melebihi stok pelanggan');
    }

    // Hitungan
    $HARGA_PER_VOLUME = 0;
    $NILAI_PER_KUPON  = 0;

    $harga_total   = $volume * $HARGA_PER_VOLUME;
    $nilai_kupon   = $pakai_kupon * $NILAI_PER_KUPON;

    // Kupon jadi minus di transaksi
    $nilai_kupon_neg = $nilai_kupon;
    $kupon_used_neg  = $pakai_kupon;

    // Hutang tetap positif
    $sisa_hutang = max($harga_total - $nilai_kupon, 0);

    // Transaksi DB
    $conn->begin_transaction();

    // Update pelanggan
    $upd = $conn->prepare("UPDATE pelanggan SET kupon = kupon - ?, hutang = hutang + ? WHERE id = ?");
    $upd->bind_param("iii", $pakai_kupon, $sisa_hutang, $id_pelanggan);
    $upd->execute();

    // Insert transaksi
    $ins = $conn->prepare("
        INSERT INTO transaksi (tanggal, nama_customer, volume, pembayaran, dibayar, hutang, kupon)
        VALUES (NOW(), ?, ?, 'Kupon', ?, ?, ?)
    ");
    $ins->bind_param("siiii", $nama_customer, $volume, $nilai_kupon_neg, $sisa_hutang, $kupon_used_neg);
    $ins->execute();

    $conn->commit();

    header("Location: ../index.php?page=transaksi&success=1");
    exit;

} catch (Throwable $e) {
    if ($conn && $conn->errno === 0) {
        $conn->rollback();
    }
    echo "Gagal simpan order: " . htmlspecialchars($e->getMessage());
}
