<?php
// clustering/proses.php

$page_title = "Proses Clustering K-Means";
require_once '../config/database.php';
require_once '../helpers/kmeans.php';
require_once '../partials/header.php';

// Cek apakah ada data siswa di database
$checkStmt = $pdo->query("SELECT COUNT(*) FROM tabel_siswa");
$countSiswa = $checkStmt->fetchColumn();

if ($countSiswa == 0) {
    $_SESSION['error'] = 'Silakan tambahkan data siswa terlebih dahulu sebelum melakukan clustering!';
    header("Location: ../siswa/index.php");
    exit();
}

$history = [];
$show_process = false;
$centroid_type = isset($_POST['centroid_type']) ? $_POST['centroid_type'] : 'proposal';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_clustering'])) {
    $kmeans = new KMeans($pdo, 3);
    
    $initialCentroids = null;
    if ($centroid_type === 'proposal') {
        // Centroid awal persis seperti yang tertulis di proposal halaman 38
        $initialCentroids = [
            'C1' => ['x1' => 0.86, 'x2' => 0.78, 'x3' => 0.50, 'x4' => 1.00],
            'C2' => ['x1' => 0.46, 'x2' => 0.65, 'x3' => 0.50, 'x4' => 0.00],
            'C3' => ['x1' => 0.62, 'x2' => 0.56, 'x3' => 1.00, 'x4' => 0.00],
        ];
    }
    
    // Jalankan K-Means
    $history = $kmeans->run($initialCentroids);
    $_SESSION['temp_assignments'] = end($history)['assignments'] ?? [];
    $show_process = true;
}

// Menangani penyimpanan hasil ke database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_hasil'])) {
    if (isset($_SESSION['temp_assignments']) && !empty($_SESSION['temp_assignments'])) {
        $kmeans = new KMeans($pdo, 3);
        $kmeans->saveResults($_SESSION['temp_assignments']);
        unset($_SESSION['temp_assignments']);
        
        $_SESSION['success'] = 'Hasil clustering K-Means berhasil disimpan ke database!';
        header("Location: hasil.php");
        exit();
    } else {
        $error = 'Tidak ada hasil clustering untuk disimpan. Silakan jalankan proses terlebih dahulu.';
    }
}
?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="text-primary fw-bold mb-1"><i class="fas fa-cogs me-2"></i>Proses Clustering K-Means</h4>
            <p class="text-muted small">Jalankan perhitungan iterasi K-Means secara dinamis dengan visualisasi step-by-step.</p>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-md-end no-print">
            <a href="normalisasi.php" class="btn btn-secondary-custom btn-sm"><i class="fas fa-arrow-left me-1"></i> Kembali ke Normalisasi</a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger py-2 px-3 small border-0 rounded-3 mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <div><?= $error; ?></div>
        </div>
    <?php endif; ?>

    <!-- Pilihan Centroid Awal & Pemicu -->
    <div class="card card-custom mb-4 no-print">
        <div class="card-header"><i class="fas fa-play-circle me-1"></i> Konfigurasi Pemrosesan</div>
        <div class="card-body">
            <form action="" method="POST">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label class="form-label small fw-semibold text-muted">Metode Pemilihan Centroid Awal</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="centroid_type" id="type_proposal" value="proposal" <?= $centroid_type === 'proposal' ? 'checked' : ''; ?>>
                                <label class="form-check-label small" for="type_proposal">
                                    Centroid Awal Proposal (Sesuai Bab III)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="centroid_type" id="type_random" value="random" <?= $centroid_type === 'random' ? 'checked' : ''; ?>>
                                <label class="form-check-label small" for="type_random">
                                    Centroid Acak Otomatis
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <button type="submit" name="proses_clustering" class="btn btn-primary-custom px-4">
                            <i class="fas fa-play me-1"></i> Proses Clustering
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($show_process && !empty($history)): ?>
        <!-- Tombol Aksi Simpan di Atas Hasil -->
        <div class="alert alert-info py-3 px-3 border-0 rounded-3 mb-4 d-flex justify-content-between align-items-center no-print">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-info-circle fs-5"></i>
                <div class="small fw-semibold">Iterasi selesai pada langkah ke-<?= count($history); ?> (Kondisi Konvergen). Simpan hasil untuk melihat laporan.</div>
            </div>
            <form action="" method="POST">
                <button type="submit" name="simpan_hasil" class="btn btn-success btn-sm px-4 fw-semibold rounded-pill">
                    <i class="fas fa-save me-1"></i> Simpan Hasil Analisis
                </button>
            </form>
        </div>

        <h5 class="text-secondary fw-bold mb-3"><i class="fas fa-history me-1"></i> Riwayat Perhitungan Step-by-Step</h5>

        <!-- Accordion Iterasi -->
        <div class="accordion mb-5" id="accordionKMeans">
            <?php foreach ($history as $iter): ?>
                <?php 
                $is_last = ($iter['iteration'] == count($history));
                $iter_id = $iter['iteration'];
                ?>
                <div class="accordion-item card-custom overflow-hidden mb-3">
                    <h2 class="accordion-header" id="heading<?= $iter_id; ?>">
                        <button class="accordion-button <?= $is_last ? '' : 'collapsed'; ?> fw-bold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $iter_id; ?>" aria-expanded="<?= $is_last ? 'true' : 'false'; ?>" aria-controls="collapse<?= $iter_id; ?>">
                            <i class="fas fa-arrow-circle-right me-2 text-secondary"></i> Iterasi Ke - <?= $iter_id; ?> <?= $is_last ? '<span class="badge bg-success ms-2 rounded-pill px-2 py-1 small fw-normal">Konvergen (Akhir)</span>' : ''; ?>
                        </button>
                    </h2>
                    <div id="collapse<?= $iter_id; ?>" class="accordion-collapse collapse <?= $is_last ? 'show' : ''; ?>" aria-labelledby="heading<?= $iter_id; ?>" data-bs-parent="#accordionKMeans">
                        <div class="accordion-body">
                            
                            <!-- Tampil Centroid Awal Iterasi -->
                            <h6 class="fw-semibold text-secondary mb-2"><i class="fas fa-bullseye me-1"></i> Koordinat Centroid Awal Iterasi</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-sm text-center small mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Centroid</th>
                                            <th>Nilai (X<sub>1</sub>)</th>
                                            <th>Kehadiran (X<sub>2</sub>)</th>
                                            <th>Keaktifan (X<sub>3</sub>)</th>
                                            <th>Dukungan (X<sub>4</sub>)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($i = 1; $i <= 3; $i++): $cLabel = 'C' . $i; ?>
                                            <tr>
                                                <td class="fw-bold text-primary"><?= $cLabel; ?></td>
                                                <td><?= number_format($iter['centroids'][$cLabel]['x1'], 4); ?></td>
                                                <td><?= number_format($iter['centroids'][$cLabel]['x2'], 4); ?></td>
                                                <td><?= number_format($iter['centroids'][$cLabel]['x3'], 4); ?></td>
                                                <td><?= number_format($iter['centroids'][$cLabel]['x4'], 4); ?></td>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabel Perhitungan Jarak & Kelompok Cluster -->
                            <h6 class="fw-semibold text-secondary mb-2"><i class="fas fa-route me-1"></i> Perhitungan Jarak Euclidean & Penentuan Cluster</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm text-center small mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">No</th>
                                            <th class="text-start">Nama Siswa</th>
                                            <th>Jarak ke C1 ($d_{C1}$)</th>
                                            <th>Jarak ke C2 ($d_{C2}$)</th>
                                            <th>Jarak ke C3 ($d_{C3}$)</th>
                                            <th>Jarak Terkecil</th>
                                            <th>Cluster</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($iter['assignments'] as $assign): ?>
                                            <?php 
                                            $c1_d = $assign['distances']['C1'];
                                            $c2_d = $assign['distances']['C2'];
                                            $c3_d = $assign['distances']['C3'];
                                            $min_d = min($c1_d, $c2_d, $c3_d);
                                            ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td class="text-start fw-medium"><?= htmlspecialchars($assign['nama_siswa']); ?></td>
                                                <td class="<?= $c1_d == $min_d ? 'text-primary fw-bold bg-light-blue' : ''; ?>"><?= number_format($c1_d, 4); ?></td>
                                                <td class="<?= $c2_d == $min_d ? 'text-primary fw-bold bg-light-blue' : ''; ?>"><?= number_format($c2_d, 4); ?></td>
                                                <td class="<?= $c3_d == $min_d ? 'text-primary fw-bold bg-light-blue' : ''; ?>"><?= number_format($c3_d, 4); ?></td>
                                                <td class="fw-semibold text-secondary"><?= number_format($min_d, 4); ?></td>
                                                <td>
                                                    <span class="badge badge-<?= strtolower($assign['cluster']); ?> font-monospace"><?= $assign['cluster']; ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
require_once '../partials/footer.php';
?>
