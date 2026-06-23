<?php
// partials/header.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hitung path ke root folder secara dinamis agar link CSS/JS tidak rusak
$path_to_root = "";
$current_dir = basename(getcwd());
if (in_array($current_dir, ["siswa", "clustering", "auth", "helpers", "config"])) {
    $path_to_root = "../";
}

// Proteksi halaman (kecuali halaman login/logout)
if (!isset($no_auth) && !isset($_SESSION['admin_logged_in'])) {
    header("Location: " . $path_to_root . "auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Sistem K-Means Siswa'; ?> - SDN 105361</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Custom Stylesheet (Tema Biru-Putih) -->
    <link rel="stylesheet" href="<?= $path_to_root; ?>assets/css/style.css">
    
    <!-- Chart.js (Untuk Grafik Dashboard & Hasil) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php if (!isset($no_layout)): ?>
<div class="wrapper">
    <!-- Sidebar -->
    <?php include $path_to_root . 'partials/sidebar.php'; ?>
    
    <!-- Page Content -->
    <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-secondary-custom btn-sm me-3 no-print">
                    <i class="fas fa-align-left"></i> Menu
                </button>
                <span class="navbar-text fw-semibold d-none d-sm-inline-block text-primary">
                    <i class="far fa-calendar-alt me-1"></i> SDN 105361 Lubuk Cemara - Sistem Analisis Nilai Siswa
                </span>
                <div class="ms-auto no-print">
                    <span class="me-3 text-muted fw-medium"><i class="fas fa-user-circle me-1"></i> <?= $_SESSION['username'] ?? 'Administrator'; ?></span>
                    <a href="<?= $path_to_root; ?>auth/logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="fas fa-sign-out-alt me-1"></i> Keluar
                    </a>
                </div>
            </div>
        </nav>
<?php endif; ?>
