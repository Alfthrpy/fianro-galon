<?php
// Pastikan sesi sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header('Location: index.php?page=login');
    exit;
}

$title = "Dashboard | Company Profile";
include "base/header.php";
?>
<body class="g-sidenav-show bg-gray-100"> <!-- penting biar Soft UI jalan -->

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-xl-none" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="index.php?page=dashboard">
            <img src="assets/img/logo ro.png" alt="Logo Galon" style="height:40px;">
            <span class="ms-2 font-weight-bold">Aplikasi Galon</span>
        </a>
    </div>  
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            <li class="nav-item">
                <a class="nav-link <?= ($_GET['page'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>" href="index.php?page=dashboard">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-tachometer-alt text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'input_order' ? 'active' : '' ?>" href="index.php?page=input_order">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-plus-circle text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Input Order</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'transaksi' ? 'active' : '' ?>" href="index.php?page=transaksi">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-history text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Transaksi</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'pelanggan' ? 'active' : '' ?>" href="index.php?page=pelanggan">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Pelanggan</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= ($_GET['page'] ?? '') === 'edit_user' ? 'active' : '' ?>" href="index.php?page=edit_user">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-cog text-dark"></i>
                    </div>
                    <span class="nav-link-text ms-1">Edit Admin</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?page=logout">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt text-danger"></i>
                    </div>
                    <span class="nav-link-text ms-1">Keluar</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<main class="main-content position-relative border-radius-lg">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3">
            <nav aria-label="breadcrumb">
                <h6 class="font-weight-bolder mb-0 text-capitalize">
                    <?= str_replace('_', ' ', $_GET['page'] ?? 'Dashboard') ?>
                </h6>
            </nav>
            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <!-- User info jadi dropdown biar mobile rapi -->
                <ul class="navbar-nav ms-auto justify-content-end">
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link text-body font-weight-bold px-0" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user me-sm-1"></i>
                            <span class="d-sm-inline d-none">Halo, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end px-2 py-3" aria-labelledby="dropdownUser">
                            <li>
                                <a class="dropdown-item" href="index.php?page=logout">
                                    <i class="fas fa-sign-out-alt me-2 text-danger"></i> Keluar
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
