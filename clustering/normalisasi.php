<?php
// clustering/normalisasi.php

$page_title = "Normalisasi Data";
require_once '../config/database.php';
require_once '../helpers/kmeans.php';
require_once '../partials/header.php';

// Cek apakah ada data siswa di database
$checkStmt = $pdo->query("SELECT COUNT(*) FROM tabel_siswa");
$countSiswa = $checkStmt->fetchColumn();

if ($countSiswa == 0) {
    $_SESSION['error'] = 'Silakan tambahkan data siswa terlebih dahulu sebelum melakukan normalisasi!';
    header("Location: ../siswa/index.php");
    exit();
}

// Jalankan helper K-Means (hanya inisialisasi untuk normalisasi)
$kmeans = new KMeans($pdo, 3);
$minMax = $kmeans->getMinMax();
$normalizedList = $kmeans->getNormalizedData();
?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="text-primary fw-bold mb-1"><i class="fas fa-calculator me-2"></i>Normalisasi Data Siswa</h4>
            <p class="text-muted small">Transformasi data asli siswa ke dalam skala 0 s.d 1 menggunakan metode <strong>Min-Max Normalization</strong>.</p>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-md-end no-print">
            <a href="proses.php" class="btn btn-primary-custom btn-sm"><i class="fas fa-arrow-right me-1"></i> Lanjut ke Proses Clustering</a>
        </div>
    </div>

    <!-- 1. Tabel Rentang Nilai Min-Max (Acuan Normalisasi) -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card card-custom">
                <div class="card-header"><i class="fas fa-sliders-h me-1"></i> Tabel Acuan Rentang Nilai (Min - Max)</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center mb-0">
                            <thead class="table-light text-primary">
                                <tr>
                                    <th>Nama Variabel / Indikator</th>
                                    <th>Nilai Minimum (X<sub>min</sub>)</th>
                                    <th>Nilai Maksimum (X<sub>max</sub>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-start fw-semibold text-dark">Nilai Rata-Rata Akademik (X<sub>1</sub>)</td>
                                    <td><?= number_format($minMax['nilai']['min'], 1); ?></td>
                                    <td><?= number_format($minMax['nilai']['max'], 1); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-semibold text-dark">Tingkat Kehadiran Siswa (X<sub>2</sub>)</td>
                                    <td><?= number_format($minMax['kehadiran']['min'], 1); ?>%</td>
                                    <td><?= number_format($minMax['kehadiran']['max'], 1); ?>%</td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-semibold text-dark">Keaktifan Siswa di Kelas (X<sub>3</sub>)</td>
                                    <td><?= number_format($minMax['keaktifan']['min'], 1); ?></td>
                                    <td><?= number_format($minMax['keaktifan']['max'], 1); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-start fw-semibold text-dark">Dukungan Orang Tua (X<sub>4</sub>)</td>
                                    <td><?= number_format($minMax['dukungan']['min'], 1); ?></td>
                                    <td><?= number_format($minMax['dukungan']['max'], 1); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Hasil Normalisasi Data -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header"><i class="fas fa-table me-1"></i> Tabel Hasil Normalisasi Data (X')</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="60">No</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Nilai (X'<sub>1</sub>)</th>
                                    <th class="text-center">Kehadiran (X'<sub>2</sub>)</th>
                                    <th class="text-center">Keaktifan (X'<sub>3</sub>)</th>
                                    <th class="text-center">Dukungan (X'<sub>4</sub>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($normalizedList as $row): ?>
                                    <tr>
                                        <td class="text-center fw-medium"><?= $no++; ?></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                        <td class="text-center fw-semibold text-primary"><?= number_format($row['x1'], 2); ?></td>
                                        <td class="text-center fw-semibold text-primary"><?= number_format($row['x2'], 2); ?></td>
                                        <td class="text-center fw-semibold text-primary"><?= number_format($row['x3'], 2); ?></td>
                                        <td class="text-center fw-semibold text-primary"><?= number_format($row['x4'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../partials/footer.php';
?>
