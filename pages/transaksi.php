<?php include "nav.php"; ?>

<style>
  /* CSS BARU: Untuk membuat tabel menjadi responsif */
  @media screen and (max-width: 768px) {
    .table-responsive-stack thead {
      display: none;
    }
    .table-responsive-stack tbody, .table-responsive-stack tr, .table-responsive-stack td {
      display: block;
      width: 100%;
    }
    .table-responsive-stack tr {
      margin-bottom: 1rem;
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      overflow: hidden;
    }
    .table-responsive-stack td {
      text-align: right;
      padding-left: 50%;
      position: relative;
      border: none;
      border-bottom: 1px solid #eee;
    }
    .table-responsive-stack tr td:last-child {
      border-bottom: none;
    }
    .table-responsive-stack td::before {
      content: attr(data-label);
      position: absolute;
      left: 0.75rem;
      width: 45%;
      padding-right: 10px;
      font-weight: bold;
      text-align: left;
    }
  }
</style>

<div class="container mt-4">
<?php
// ... (Bagian PHP untuk query data tidak perlu diubah) ...
require_once __DIR__ . '/../config/db.php';
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

$query = "SELECT * FROM transaksi $sql_where ORDER BY id DESC";
$result = $conn->query($query);

$query_total = $conn->query("
SELECT 
    SUM(volume) AS total_pesanan,
    SUM(dibayar) AS total_dibayar, 
    SUM(hutang) AS total_hutang, 
    SUM(kupon) AS total_kupon,
    COUNT(nama_customer) AS total_transaksi,
    SUM(CASE WHEN pembayaran = 'Tunai' THEN dibayar ELSE 0 END) AS total_tunai,
    SUM(CASE WHEN pembayaran = 'Kupon' THEN dibayar ELSE 0 END) AS total_bayar_kupon,
    (SUM(dibayar) + SUM(hutang)) AS total_pendapatan
FROM transaksi $sql_where
") or die($conn->error);
$total = $query_total->fetch_assoc();
?>
<div class="row">
    <div class="col-12 col-md-6 col-lg-4 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md me-3">
              <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <div class="text-start">
              <p class="text-sm mb-0 text-capitalize">Total Pendapatan</p>
              <h5 class="mb-0">Rp <?= number_format($total['total_pendapatan'], 0, ',', '.') ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md me-3">
              <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <div class="text-start">
              <p class="text-sm mb-0 text-capitalize">Total bayar</p>
              <h5 class="mb-0">Rp <?= number_format($total['total_tunai'], 0, ',', '.') ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md me-3">
              <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div class="text-start">
              <p class="text-sm mb-0 text-capitalize">Total Hutang</p>
              <h5 class="mb-0">Rp <?= number_format($total['total_hutang'], 0, ',', '.') ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md me-3">
              <i class="fas fa-ticket-alt text-white"></i>
            </div>
            <div class="text-start">
              <p class="text-sm mb-0 text-capitalize">Kupon Terkumpul</p>
              <h5 class="mb-0"><?= number_format($total['total_kupon'], 0, ',', '.') ?> kupon</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md me-3">
              <i class="fas fa-shopping-cart text-white"></i>
            </div>
            <div class="text-start">
              <p class="text-sm mb-0 text-capitalize">Jumlah Galon</p>
              <h5 class="mb-0"><?= number_format($total['total_pesanan']) ?> Pesanan</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="d-flex">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md me-3">
              <i class="fas fa-receipt text-white"></i>
            </div>
            <div class="text-start">
              <p class="text-sm mb-0 text-capitalize">Jumlah transaksi</p>
              <h5 class="mb-0"><?= number_format($total['total_transaksi'], 0, ',', '.') ?> Transaksi</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="card">
  <div class="card-body">
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="transaksi">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-lg-4">
                <input type="text" name="search_nama" class="form-control" placeholder="Cari Nama Customer..." value="<?= isset($_GET['search_nama']) ? htmlspecialchars($_GET['search_nama']) : '' ?>">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <input type="date" name="start_date" class="form-control" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <input type="date" name="end_date" class="form-control" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
            </div>
            <div class="col-12 col-lg-2">
                <div class="d-grid d-lg-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                    <a href="index.php?page=transaksi" class="btn btn-secondary"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12 text-end">
                <a href="pages/export_pd.php?<?= http_build_query($_GET) ?>" class="btn btn-danger">
                    <i class="fa-solid fa-file-pdf me-1"></i> Ekspor PDF
                </a>
            </div>
        </div>
    </form>
  </div>
  
  <div class="table-responsive">
    <table class="table align-items-center mb-0 table-responsive-stack">
      <thead>
        <tr>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tanggal</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Customer</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Volume</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pembayaran</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dibayar</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Hutang</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Kupon</th>
          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td data-label="Tanggal">
                <p class="text-xs font-weight-bold mb-0"><?= date('d-m-Y', strtotime($row['tanggal'])) ?></p>
              </td>
              <td data-label="Customer">
                <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($row['nama_customer']) ?></p>
              </td>
              <td data-label="Volume">
                <p class="text-xs font-weight-bold mb-0"><?= htmlspecialchars($row['volume']) ?></p>
              </td>
              <td data-label="Pembayaran">
                <span class="badge badge-sm <?= $row['pembayaran'] === 'tunai' ? 'bg-gradient-success' : 'bg-gradient-info' ?>">
                  <?= ucfirst($row['pembayaran']) ?>
                </span>
              </td>
              <td data-label="Dibayar">
                <p class="text-xs font-weight-bold mb-0">Rp <?= number_format($row['dibayar'], 0, ',', '.') ?></p>
              </td>
              <td data-label="Hutang">
                <p class="text-xs text-danger mb-0">Rp <?= number_format($row['hutang'], 0, ',', '.') ?></p>
              </td>
              <td data-label="Kupon">
                <p class="text-xs font-weight-bold mb-0"><?= $row['kupon'] ?></p>
              </td>
              <td data-label="Aksi" class="text-center">
                <form method="POST" action="pages/hapus_transaksi.php" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                  <input type="hidden" name="id" value="<?= $row['id']; ?>">
                  <button type="submit" class="btn btn-danger btn-sm"><i class="fa-regular fa-trash-can"></i> Hapus</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="text-center text-secondary py-4">Belum ada data transaksi yang sesuai dengan filter.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<?php include "base/footer.php"; ?>