<?php
// session_start(); // Pastikan session_start() ada di bagian paling atas, mungkin di header.php

if (isset($_SESSION['username'])) {
    header('Location: index.php?page=dashboard');
    exit;
}

$title = "Login | Company Profile";
include "base/header.php";
?>

<style>
    body {
        background-color: #f8f9fa; /* Warna abu-abu muda */
    }
</style>

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-5 col-lg-4">
            
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    
                    <div class="text-center mb-4">
                        <img src="assets/img/logo ro.png" alt="Logo Perusahaan" style="max-width: 150px;">
                        <h4 class="mt-3 mb-0">Login Admin</h4>
                    </div>

                    <form action="auth/login_process.php" method="POST">
                        
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        
                        <div class="input-group mb-4">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger py-2 text-center">
                                Username atau password salah!
                            </div>
                        <?php endif; ?>
                        
                        <div class="d-grid">
                           <button type="submit" class="btn btn-primary">Login</button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include "base/footer.php"; ?>