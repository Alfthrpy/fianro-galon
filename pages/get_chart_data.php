<?php
// Set header ke JSON karena kita akan mengembalikan data dalam format JSON
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Ambil parameter filter dari URL, defaultnya 'bulan'
$filter = $_GET['filter'] ?? 'bulan';

// Siapkan klausa WHERE berdasarkan filter
$whereClause = '';
switch ($filter) {
    case 'hari':
        // Data untuk hari ini
        $whereClause = "WHERE DATE(tanggal) = CURDATE()";
        break;
    case 'minggu':
        // Data untuk minggu ini (Senin sebagai hari pertama)
        $whereClause = "WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'tahun':
        // Data untuk tahun ini
        $whereClause = "WHERE YEAR(tanggal) = YEAR(CURDATE())";
        break;
    case 'bulan':
    default:
        // Data untuk bulan ini (default)
        $whereClause = "WHERE YEAR(tanggal) = YEAR(CURDATE()) AND MONTH(tanggal) = MONTH(CURDATE())";
        break;
}

// Query utama dengan klausa WHERE yang dinamis
$query_chart = $conn->query("
    SELECT 
        DATE(tanggal) AS tgl,
        SUM(dibayar) + SUM(hutang) AS total_pendapatan,
        SUM(hutang) AS total_hutang
    FROM transaksi
    $whereClause
    GROUP BY DATE(tanggal)
    ORDER BY tgl ASC
");

// Siapkan array untuk dikirim kembali
$labels = [];
$data_pendapatan = [];
$data_hutang = [];

while ($row = $query_chart->fetch_assoc()) {
    $labels[] = date('d M Y', strtotime($row['tgl'])); // Format tanggal agar lebih mudah dibaca
    $data_pendapatan[] = $row['total_pendapatan'];
    $data_hutang[] = $row['total_hutang'];
}

// Kembalikan data dalam format JSON
echo json_encode([
    'labels' => $labels,
    'data_pendapatan' => $data_pendapatan,
    'data_hutang' => $data_hutang,
]);

exit;