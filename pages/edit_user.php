<?php 

// 2. Cek apakah pengguna sudah login. Jika belum, tendang ke halaman login.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Ganti 'login.php' dengan halaman login Anda
    exit(); // Pastikan script berhenti setelah redirect
}

include "nav.php";
require_once __DIR__ . '/../config/db.php';

// 3. Ambil ID dari session yang sedang aktif
$id = $_SESSION['user_id'];

// Ambil data user dari database
$stmt = $conn->prepare("SELECT * FROM user WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // Ini jarang terjadi jika session valid, tapi baik untuk keamanan
    die("User tidak ditemukan.");
}
?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h3 class="card-title text-center mb-4">Edit Profil Saya</h3>

          <?php if (isset($_SESSION['message'])): ?>
              <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
                  <?= $_SESSION['message']; ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
          <?php endif; ?>
          
          <form id="editUserForm" method="post" action="../pages/update_user.php">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password Baru</label>
              <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin ganti">
              </div>
            </div>

            <div class="mb-3">
              <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ketik ulang password baru">
              <div class="invalid-feedback">Password tidak cocok.</div>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
              <a href="../index.php?page=dashboard" class="btn btn-secondary">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>



<?php include "base/footer.php"; ?>