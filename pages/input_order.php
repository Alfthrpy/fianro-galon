<?php include "nav.php"; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
  /* CSS BARU: Untuk membuat tabel menjadi responsif */
  @media screen and (max-width: 768px) {
    #hutangTable thead {
      display: none;
    }

    #hutangTable tbody,
    #hutangTable tr,
    #hutangTable td {
      display: block;
      width: 100%;
    }

    #hutangTable tr {
      margin-bottom: 1rem;
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      overflow: hidden;
      /* agar radius terlihat */
    }

    #hutangTable td {
      text-align: right;
      padding-left: 50%;
      position: relative;
      border: none;
      border-bottom: 1px solid #eee;
    }

    #hutangTable tr td:last-child {
      border-bottom: none;
    }

    #hutangTable td::before {
      content: attr(data-label);
      position: absolute;
      left: 0.75rem;
      width: 45%;
      padding-right: 10px;
      font-weight: bold;
      text-align: left;
    }

    /* GANTI ATURAN LAMA ANDA DENGAN YANG INI */
    #hutangTable td[data-label="Aksi"] {
      padding-top: 1rem;
      padding-bottom: 1rem;

      /* Aturan baru untuk tata letak tombol yang fleksibel */
      display: flex;
      justify-content: flex-end;
      /* Sejajarkan tombol ke kanan */
      align-items: center;
      flex-wrap: wrap;
      /* Izinkan tombol turun jika tidak cukup ruang */
      gap: 0.5rem;
      /* Beri jarak antar tombol */
    }
  }
</style>

<div class="container mt-4">

  <?php
  require_once __DIR__ . '/../config/db.php';
  // Query pelanggan tidak perlu lagi di sini karena sudah ada di dalam modal
  ?>
  <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#modalInputOrder">
    + Tambah Order
  </button>

  <div class="modal fade" id="modalInputOrder" tabindex="-1" aria-labelledby="modalInputOrderLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="pages/simpan_order.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="modalInputOrderLabel">Input Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="tipe_customer">Tipe Customer:</label><br>
              <input type="radio" name="tipe_customer" value="lama" checked onclick="toggleCustomerForm()"> Customer Lama
              <input type="radio" name="tipe_customer" value="baru" onclick="toggleCustomerForm()"> Customer Baru
            </div>
            <div class="mb-3" id="customer_lama">
              <label for="selectCustomerLama" class="form-label">Pilih Customer Lama:</label>
              <select name="nama_customer_lama" id="selectCustomerLama" class="form-select">
                <option value="">Pilih Customer</option>
                <?php
                // include '../db/db.php'; // Pastikan path ini benar
                $result = mysqli_query($conn, "SELECT nama_customer FROM pelanggan ORDER BY nama_customer ASC");
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value='" . htmlspecialchars($row['nama_customer']) . "'>" . htmlspecialchars($row['nama_customer']) . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="mb-3" id="customer_baru" style="display: none;">
              <label for="nama_customer_baru" class="form-label">Nama Customer Baru:</label>
              <input type="text" name="nama_customer_baru" class="form-control">
              <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" name="simpan_customer_baru" id="simpan_customer_baru">
                <label class="form-check-label" for="simpan_customer_baru">
                  Simpan ke database pelanggan
                </label>
              </div>
            </div>
            <input type="hidden" name="pembayaran" value="tunai">
            <div class="mb-3">
              <label for="tipe_pembelian">Tipe Pembelian:</label><br>
              <input type="radio" id="perorangan" name="tipe_pembelian" value="perorangan" checked>
              <label for="perorangan">Perorangan (Rp 5.000/liter)</label><br>
              <input type="radio" id="massal" name="tipe_pembelian" value="massal">
              <label for="massal">Massal/Reseller (Rp 4.000/liter)</label>
            </div>
            <div class="mb-3">
              <label class="form-label">Volume Pembelian (Liter):</label>
              <input type="number" name="volume_pembelian" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header pb-0">
      <form method="GET" action="index.php">
        <input type="hidden" name="page" value="input_order">
        <div class="row g-2 align-items-center mb-3">
          <div class="col-12 col-lg-4">
            <label for="search_nama" class="visually-hidden">Cari Nama</label>
            <input type="text" name="search_nama" id="search_nama" class="form-control" placeholder="Cari Nama Customer..." value="<?= isset($_GET['search_nama']) ? htmlspecialchars($_GET['search_nama']) : '' ?>">
          </div>
          <div class="col-12 col-sm-6 col-lg-3">
            <label for="start_date" class="visually-hidden">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
          </div>
          <div class="col-12 col-sm-6 col-lg-3">
            <label for="end_date" class="visually-hidden">Tanggal Selesai</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
          </div>
          <div class="col-12 col-lg-2">
            <div class="d-grid d-lg-flex gap-2">
              <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i>Filter</button>
              <a href="index.php?page=input_order" class="btn btn-secondary"><i class="fa-solid fa-rotate-left me-1"></i>Reset</a>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
      <div class="table-responsive p-0">
        <table class="table align-items-center mb-0" id="hutangTable">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Tanggal</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Customer</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Metode</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dibayar (Rp)</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hutang (Rp)</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kupon</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Volume</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $where = ["hutang > 0"];
            if (!empty($_GET['search_nama'])) {
              $nama = $conn->real_escape_string($_GET['search_nama']);
              $where[] = "nama_customer LIKE '%$nama%'";
            }
            if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
              $start_date = $conn->real_escape_string($_GET['start_date']);
              $end_date   = $conn->real_escape_string($_GET['end_date']);
              $where[] = "DATE(tanggal) BETWEEN '$start_date' AND '$end_date'";
            } elseif (!empty($_GET['start_date'])) {
              $start_date = $conn->real_escape_string($_GET['start_date']);
              $where[] = "DATE(tanggal) >= '$start_date'";
            } elseif (!empty($_GET['end_date'])) {
              $end_date = $conn->real_escape_string($_GET['end_date']);
              $where[] = "DATE(tanggal) <= '$end_date'";
            }

            $sql_where = "WHERE " . implode(" AND ", $where);
            $query = "SELECT * FROM transaksi $sql_where ORDER BY id DESC";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
            ?>
              <tr>
                <td data-label="Tanggal" class="ps-3">
                  <p class="text-xs mb-0"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></p>
                </td>
                <td data-label="Nama Customer">
                  <p class="text-xs font-weight-bold mb-0 nama-customer"><?php echo htmlspecialchars($row['nama_customer']); ?></p>
                </td>
                <td data-label="Metode">
                  <p class="text-xs mb-0"><?php echo $row['pembayaran']; ?></p>
                </td>
                <td data-label="Dibayar">
                  <p class="text-xs mb-0">Rp. <?php echo number_format($row['dibayar'], 0, ',', '.'); ?></p>
                </td>
                <td data-label="Hutang">
                  <p class="text-xs text-danger mb-0">Rp. <?php echo number_format($row['hutang'], 0, ',', '.'); ?></p>
                </td>
                <td data-label="Kupon">
                  <p class="text-xs mb-0"><?php echo $row['kupon']; ?></p>
                </td>
                <td data-label="Volume">
                  <p class="text-xs mb-0"><?php echo $row['volume']; ?> Galon</p>
                </td>
                <td data-label="Aksi">
                  <button class="btn btn-success btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#modalBayar<?= $row['id'] ?>">Bayar</button>
                  <form method="POST" action="pages/hapus_transaksia.php" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm mb-1">Hapus</button>
                  </form>
                </td>

                <div class="modal fade" id="modalBayar<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalBayarLabel<?= $row['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="pages/proses_bayar.php" method="POST">
                      <input type="hidden" name="id_transaksi" value="<?= $row['id'] ?>">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalBayarLabel<?= $row['id'] ?>">Form Pembayaran</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label>Nama Customer:</label>
                            <input type="text" class="form-control" name="nama_customer" value="<?= htmlspecialchars($row['nama_customer']) ?>" readonly>
                          </div>
                          <div class="mb-3">
                            <label>Hutang (Rp):</label>
                            <input type="text" class="form-control" value="<?= number_format($row['hutang'], 0, ',', '.') ?>">
                            <input type="hidden" name="hutang" value="<?= $row['hutang'] ?>">
                            <input type="hidden" name="volume" value="<?= $row['volume'] ?>">
                          </div>
                          <div class="mb-3">
                            <label>Metode Pembayaran:</label>
                            <input type="text" class="form-control" name="pembayaran" value="<?= htmlspecialchars($row['pembayaran']) ?>" readonly>
                          </div>
                          <div class="mb-3">
                            <label>Jumlah Dibayar (Rp):</label>
                            <input type="number" class="form-control" name="dibayar" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary">Bayar</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  // Fungsi untuk toggle form customer lama/baru
  function toggleCustomerForm() {
    const tipe = document.querySelector('input[name="tipe_customer"]:checked').value;
    document.getElementById('customer_lama').style.display = tipe === 'lama' ? 'block' : 'none';
    document.getElementById('customer_baru').style.display = tipe === 'baru' ? 'block' : 'none';
  }

  // Inisialisasi saat halaman dimuat
  window.onload = toggleCustomerForm;

  // Inisialisasi Select2
  document.addEventListener('DOMContentLoaded', function() {
    const modalInputOrder = document.getElementById('modalInputOrder');
    modalInputOrder.addEventListener('shown.bs.modal', function() {
      $('#selectCustomerLama').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalInputOrder')
      });
    });
  });
</script>

<?php include "base/footer.php"; ?>