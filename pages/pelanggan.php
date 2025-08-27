<?php include "nav.php"; ?>

<style>
  /* CSS untuk membuat tabel menjadi responsif */
  @media screen and (max-width: 768px) {
    .table-responsive-stack thead {
      display: none;
    }

    .table-responsive-stack tbody,
    .table-responsive-stack tr,
    .table-responsive-stack td {
      display: block;
      width: 100%;
    }

    .table-responsive-stack tr {
      margin-bottom: 1rem;
      border: 1px solid #dee2e6;
      border-radius: .375rem;
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
      left: .75rem;
      width: 45%;
      padding-right: 10px;
      font-weight: bold;
      text-align: left;
    }
  }
</style>

<div class="container mt-4">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h6>Data Pelanggan</h6>
    </div>
    <div class="card-body px-3 pt-3 pb-2">
      <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Cari nama pelanggan...">
      </div>

      <div class="table-responsive p-0">
        <table class="table align-items-center mb-0 table-responsive-stack" id="pelangganTable">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Customer</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hutang (Rp)</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kupon</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            require_once __DIR__ . '/../config/db.php';
            $query = "SELECT * FROM pelanggan ORDER BY id DESC";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
            ?>
              <tr>
                <td data-label="ID" class="text-xs ps-4"><?php echo $row['id']; ?></td>
                <td data-label="Nama Customer">
                  <p class="text-xs font-weight-bold mb-0 nama-customer"><?php echo htmlspecialchars($row['nama_customer']); ?></p>
                </td>
                <td data-label="Hutang">
                  <p class="text-xs mb-0 text-danger">Rp. <?php echo number_format($row['hutang'], 0, ',', '.'); ?></p>
                </td>
                <td data-label="Kupon">
                  <p class="text-xs mb-0"><?php echo $row['kupon']; ?></p>
                </td>
                <td data-label="Aksi" class="text-center">
                  <div class="dropdown">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton-<?= $row['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                      Aksi
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-<?= $row['id']; ?>">
                      <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalInputOrder" data-id="<?= $row['id']; ?>" data-nama="<?= htmlspecialchars($row['nama_customer']); ?>" data-kupon="<?= $row['kupon']; ?>">Pakai Kupon Galon</a></li>
                      <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalAturKupon" data-id="<?= $row['id']; ?>" data-nama="<?= htmlspecialchars($row['nama_customer']); ?>" data-aksi="tambah">+ Tambah Kupon</a></li>
                      <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalAturKupon" data-id="<?= $row['id']; ?>" data-nama="<?= htmlspecialchars($row['nama_customer']); ?>" data-aksi="kurangi">- Kurangi Kupon</a></li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li><a class="dropdown-item text-danger" href="pages/hapus_pelanggan.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin hapus pelanggan ini?')">Hapus Pelanggan</a></li>
                    </ul>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modalAturKupon" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="pages/proses_kupon.php">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAturKuponTitle"></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="kupon_id_pelanggan">
          <p>Customer: <strong id="kupon_nama_pelanggan"></strong></p>
          <div class="mb-3">
            <label class="form-label">Jumlah Kupon</label>
            <input type="number" class="form-control" name="jumlah" min="1" value="1" required>
          </div>
        </div>
        <div class="modal-footer" id="modalAturKuponFooter">
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalInputOrder" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="pages/simpan_order_kupon.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Order dengan Kupon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          
          <input type="hidden" name="id_pelanggan" id="order_id">

          <div class="mb-3">
            <label>Nama Customer</label>
            <input type="text" class="form-control" id="order_nama" name="nama_customer" readonly>
          </div>

          <div class="mb-3">
            <label>Jumlah Kupon</label>
            <input type="text" class="form-control" id="order_kupon" name="kupon" readonly>
          </div>

          <div class="mb-3">
            <label>Volume (Galon)</label>
            <input type="number" class="form-control" id="order_volume" name="volume" min="1" required>
          </div>

          <div class="mb-3">
            <label>Gunakan Kupon</label>
            <input type="number" class="form-control" id="order_pakai_kupon" name="pakai_kupon" min="0" required>
          </div>

          <div class="mb-3">
            <label>Total Harga</label>
            <input type="text" class="form-control" id="order_total" readonly>
          </div>

          <div class="mb-3">
            <label>Nilai Kupon (Rp)</label>
            <input type="text" class="form-control" id="order_nilai_kupon" readonly>
          </div>

          <div class="mb-3">
            <label>Sisa Hutang (Rp)</label>
            <input type="text" class="form-control" id="order_hutang" name="hutang" readonly>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Order</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // SCRIPT PENCARIAN
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('pelangganTable');
    const rows = table.getElementsByTagName('tr');

    searchInput.addEventListener('keyup', function() {
      const filter = searchInput.value.toUpperCase();
      for (let i = 1; i < rows.length; i++) { // Mulai dari 1 untuk skip header
        let namaTd = rows[i].getElementsByClassName('nama-customer')[0];
        if (namaTd) {
          let textValue = namaTd.textContent || namaTd.innerText;
          if (textValue.toUpperCase().indexOf(filter) > -1) {
            rows[i].style.display = "";
          } else {
            rows[i].style.display = "none";
          }
        }
      }
    });

    // SCRIPT UNTUK MODAL ATUR KUPON (TAMBAH/KURANGI)
    const modalAturKupon = document.getElementById('modalAturKupon');
    modalAturKupon.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const nama = button.getAttribute('data-nama');
      const aksi = button.getAttribute('data-aksi');

      const modalTitle = document.getElementById('modalAturKuponTitle');
      const modalFooter = document.getElementById('modalAturKuponFooter');

      document.getElementById('kupon_id_pelanggan').value = id;
      document.getElementById('kupon_nama_pelanggan').innerText = nama;

      // Atur judul dan tombol submit berdasarkan aksi
      if (aksi === 'tambah') {
        modalTitle.innerText = 'Tambah Kupon';
        modalFooter.innerHTML = '<button type="submit" name="tambah" class="btn btn-success">Tambah</button>';
      } else {
        modalTitle.innerText = 'Kurangi Kupon';
        modalFooter.innerHTML = '<button type="submit" name="kurangi" class="btn btn-danger">Kurangi</button>';
      }
    });

    // SCRIPT UNTUK MODAL ORDER DENGAN KUPON
    const modalInputOrder = document.getElementById('modalInputOrder');
    modalInputOrder.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const nama = button.getAttribute('data-nama');
      const kupon = button.getAttribute('data-kupon');

      document.getElementById('order_id').value = id;
      document.getElementById('order_nama').value = nama;
      document.getElementById('order_kupon').value = kupon;
      document.getElementById('order_pakai_kupon').max = kupon;
      document.getElementById('order_pakai_kupon').value = 0; // Reset
      document.getElementById('order_volume').value = 0; // Reset
      hitungTotal(); // Panggil hitung untuk reset field lainnya
    });

    function hitungTotal() {
      let volume = parseInt(document.getElementById('order_volume').value) || 0;
      let hargaPerGalon = 5000;
      let hargaTotal = volume * hargaPerGalon;

      let kuponDimiliki = parseInt(document.getElementById('order_kupon').value) || 0;
      let pakaiKupon = parseInt(document.getElementById('order_pakai_kupon').value) || 0;

      // Pastikan kupon yang dipakai tidak melebihi yang dimiliki
      if (pakaiKupon > kuponDimiliki) {
        pakaiKupon = kuponDimiliki;
        document.getElementById('order_pakai_kupon').value = pakaiKupon;
      }

      let nilaiPerKupon = 500;
      let nilaiKupon = pakaiKupon * nilaiPerKupon;
      let hutang = Math.max(0, hargaTotal - nilaiKupon);

      document.getElementById('order_total').value = 'Rp ' + hargaTotal.toLocaleString('id-ID');
      document.getElementById('order_nilai_kupon').value = 'Rp ' + nilaiKupon.toLocaleString('id-ID');
      document.getElementById('order_hutang').value = 'Rp ' + hutang.toLocaleString('id-ID');
    }

    document.getElementById('order_volume').addEventListener('input', hitungTotal);
    document.getElementById('order_pakai_kupon').addEventListener('input', hitungTotal);
  });
</script>

<?php include "base/footer.php"; ?>