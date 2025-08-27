<?php
require_once __DIR__ . '/../config/vendor/autoload.php'; // composer mPDF
include "../config/db.php";
use Mpdf\Mpdf;

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

// Gabungkan kondisi WHERE
$sql_where = "";
if (!empty($where)) {
    $sql_where = "WHERE " . implode(" AND ", $where);
}

// Query data transaksi (LIST)
$query = "SELECT * FROM transaksi $sql_where ORDER BY tanggal DESC";
$result = $conn->query($query);

// Query total (sinkron dengan halaman list)
$query_total = $conn->query("
    SELECT 
        SUM(volume) AS total_pesanan,
        SUM(dibayar) AS total_dibayar, 
        SUM(hutang) AS total_hutang, 
        SUM(kupon) AS total_kupon,
        SUM(CASE WHEN pembayaran = 'Tunai' THEN dibayar ELSE 0 END) AS total_tunai,
        SUM(CASE WHEN pembayaran = 'Kupon' THEN dibayar ELSE 0 END) AS total_bayar_kupon,
        (SUM(dibayar) + SUM(hutang)) AS total_pendapatan
    FROM transaksi
    $sql_where
") or die($conn->error);

$total = $query_total->fetch_assoc();

// Mulai buffer HTML
ob_start();
?>
<h2 style="text-align:center;">Laporan Transaksi</h2>
<p style="text-align:center; font-size:12px;">
    <?= !empty($_GET['start_date']) ? "Dari: ".$_GET['start_date'] : "" ?>
    <?= !empty($_GET['end_date']) ? "Sampai: ".$_GET['end_date'] : "" ?>
    <?= !empty($_GET['search_nama']) ? "<br>Customer: ".$_GET['search_nama'] : "" ?>
</p>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Customer</th>
            <th>Pembayaran</th>
            <th>Dibayar</th>
            <th>Hutang</th>
            <th>Kupon</th>
            <th>Volume</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
            <td><?= htmlspecialchars($row['nama_customer']) ?></td>
            <td><?= $row['pembayaran'] ?></td>
            <td>Rp <?= number_format($row['dibayar'], 0, ',', '.') ?></td>
            <td>Rp <?= number_format($row['hutang'], 0, ',', '.') ?></td>
            <td><?= $row['kupon'] ?></td>
            <td><?= $row['volume'] ?> Galon</td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<br>
<h3>Ringkasan</h3>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <tr>
        <td>Total Volume</td>
        <td><?= $total['total_pesanan'] ?> Galon</td>
    </tr>
    <tr>
        <td>Total Dibayar</td>
        <td>Rp <?= number_format($total['total_dibayar'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td>Total Hutang</td>
        <td>Rp <?= number_format($total['total_hutang'], 0, ',', '.') ?></td>
    </tr>
    <tr>
        <td>Total Kupon</td>
        <td><?= $total['total_kupon'] ?></td>
    </tr>
    <tr>
        <td>Total Pendapatan</td>
        <td>Rp <?= number_format($total['total_pendapatan'], 0, ',', '.') ?></td>
    </tr>
</table>
<?php
$html = ob_get_clean();

// Generate PDF
$mpdf = new Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output("laporan_transaksi.pdf", "I"); // I = tampil di browser
