<?php
require_once __DIR__ . '/../config/db.php';

// Ambil filter yang sama dengan halaman utama
$where = [];

// Filter nama customer
if (!empty($_GET['search_nama'])) {
    $nama = $conn->real_escape_string($_GET['search_nama']);
    $where[] = "nama_customer LIKE '%$nama%'";
}

// Filter tanggal
if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $start_date = $conn->real_escape_string($_GET['start_date']);
    $end_date   = $conn->real_escape_string($_GET['end_date']);
    $where[] = "tanggal BETWEEN '$start_date' AND '$end_date'";
} elseif (!empty($_GET['start_date'])) {
    $start_date = $conn->real_escape_string($_GET['start_date']);
    $where[] = "tanggal >= '$start_date'";
} elseif (!empty($_GET['end_date'])) {
    $end_date = $conn->real_escape_string($_GET['end_date']);
    $where[] = "tanggal <= '$end_date'";
}

$sql_where = "";
if (!empty($where)) {
    $sql_where = "WHERE " . implode(" AND ", $where);
}

// Ambil data transaksi sesuai filter
$query = "SELECT * FROM transaksi $sql_where ORDER BY tanggal DESC";
$result = $conn->query($query);

// Set header untuk download CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=transaksi.csv');

// Buat output CSV
$output = fopen('php://output', 'w');

// Header kolom CSV
fputcsv($output, ['Tanggal', 'Customer', 'Volume', 'Pembayaran', 'Dibayar', 'Hutang', 'Kupon']);

// Isi data
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        date('d-m-Y', strtotime($row['tanggal'])),
        $row['nama_customer'],
        $row['volume'],
        ucfirst($row['pembayaran']),
        $row['dibayar'],
        $row['hutang'],
        $row['kupon']
    ]);
}

fclose($output);
exit;
