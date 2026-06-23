<?php
// partials/sidebar.php

// Tentukan menu aktif berdasarkan nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(getcwd());
?>
<nav id="sidebar" class="no-print">
    <div class="sidebar-header">
        <h5>
            <i class="fas fa-chart-pie"></i>
            <span>K-Means Siswa</span>
        </h5>
    </div>

    <ul class="list-unstyled components">
        <li class="<?= ($current_page == 'index.php' && $current_dir == 'kmeansriza') ? 'active' : ''; ?>">
            <a href="<?= $path_to_root; ?>index.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="<?= ($current_dir == 'siswa') ? 'active' : ''; ?>">
            <a href="<?= $path_to_root; ?>siswa/index.php">
                <i class="fas fa-graduation-cap"></i>
                <span>Data Siswa</span>
            </a>
        </li>

        <li class="<?= ($current_page == 'normalisasi.php') ? 'active' : ''; ?>">
            <a href="<?= $path_to_root; ?>clustering/normalisasi.php">
                <i class="fas fa-calculator"></i>
                <span>Normalisasi Data</span>
            </a>
        </li>

        <li class="<?= ($current_page == 'proses.php') ? 'active' : ''; ?>">
            <a href="<?= $path_to_root; ?>clustering/proses.php">
                <i class="fas fa-cogs"></i>
                <span>Proses Clustering</span>
            </a>
        </li>

        <li class="<?= ($current_page == 'hasil.php') ? 'active' : ''; ?>">
            <a href="<?= $path_to_root; ?>clustering/hasil.php">
                <i class="fas fa-chart-bar"></i>
                <span>Hasil Clustering</span>
            </a>
        </li>

        <li class="<?= ($current_page == 'cetak.php') ? 'active' : ''; ?>">
            <a href="<?= $path_to_root; ?>clustering/cetak.php">
                <i class="fas fa-print"></i>
                <span>Cetak Laporan</span>
            </a>
        </li>
    </ul>
</nav>
