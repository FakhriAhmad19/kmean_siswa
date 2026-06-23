<?php
// clustering/hasil.php

$page_title = "Hasil Clustering";
require_once '../config/database.php';
require_once '../partials/header.php';

// Ambil data hasil clustering beserta detail siswa
$query = "SELECT s.*, hc.cluster_label, hc.jarak_c1, hc.jarak_c2, hc.jarak_c3 
          FROM tabel_siswa s 
          INNER JOIN tabel_hasil_clustering hc ON s.id_siswa = hc.id_siswa
          ORDER BY hc.cluster_label ASC, s.nama_siswa ASC";
$stmt = $pdo->query($query);
$results = $stmt->fetchAll();

$countC1 = 0;
$countC2 = 0;
$countC3 = 0;

$avgC1 = ['nilai' => 0, 'hadir' => 0, 'aktif' => 0, 'dukung' => 0];
$avgC2 = ['nilai' => 0, 'hadir' => 0, 'aktif' => 0, 'dukung' => 0];
$avgC3 = ['nilai' => 0, 'hadir' => 0, 'aktif' => 0, 'dukung' => 0];

if (!empty($results)) {
    foreach ($results as $row) {
        $c = $row['cluster_label'];
        if ($c === 'C1') {
            $countC1++;
            $avgC1['nilai'] += $row['nilai_akademik'];
            $avgC1['hadir'] += $row['kehadiran_siswa'];
            $avgC1['aktif'] += $row['keaktifan_siswa'];
            $avgC1['dukung'] += $row['dukungan_ortu'];
        } elseif ($c === 'C2') {
            $countC2++;
            $avgC2['nilai'] += $row['nilai_akademik'];
            $avgC2['hadir'] += $row['kehadiran_siswa'];
            $avgC2['aktif'] += $row['keaktifan_siswa'];
            $avgC2['dukung'] += $row['dukungan_ortu'];
        } elseif ($c === 'C3') {
            $countC3++;
            $avgC3['nilai'] += $row['nilai_akademik'];
            $avgC3['hadir'] += $row['kehadiran_siswa'];
            $avgC3['aktif'] += $row['keaktifan_siswa'];
            $avgC3['dukung'] += $row['dukungan_ortu'];
        }
    }
    
    // Hitung rata-rata per cluster
    if ($countC1 > 0) {
        $avgC1 = array_map(function($val) use ($countC1) { return $val / $countC1; }, $avgC1);
    }
    if ($countC2 > 0) {
        $avgC2 = array_map(function($val) use ($countC2) { return $val / $countC2; }, $avgC2);
    }
    if ($countC3 > 0) {
        $avgC3 = array_map(function($val) use ($countC3) { return $val / $countC3; }, $avgC3);
    }
}

// Fungsi helper untuk kategori keaktifan & dukungan (rata-rata)
function getAveragedLabel($val) {
    if ($val >= 2.5) return 'Sangat Tinggi / Sangat Baik';
    if ($val >= 1.7) return 'Sedang / Cukup';
    return 'Rendah / Kurang';
}
?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="text-primary fw-bold mb-1"><i class="fas fa-chart-bar me-2"></i>Hasil Analisis Clustering</h4>
            <p class="text-muted small">Rangkuman hasil pembagian siswa ke dalam 3 kelompok cluster berdasarkan kemiripan karakteristik.</p>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-md-end gap-2 no-print">
            <a href="proses.php" class="btn btn-secondary-custom btn-sm"><i class="fas fa-sync me-1"></i> Ulangi Proses</a>
            <a href="cetak.php" class="btn btn-primary-custom btn-sm"><i class="fas fa-print me-1"></i> Cetak Laporan</a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success py-2 px-3 small border-0 rounded-3 mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <div><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
    <?php endif; ?>

    <?php if (empty($results)): ?>
        <div class="card card-custom">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-4"><i class="fas fa-info-circle fs-1 text-primary"></i></div>
                <h5 class="fw-bold">Hasil Clustering Belum Tersedia</h5>
                <p class="text-muted small mb-4">Silakan lakukan proses clustering terlebih dahulu untuk mengelompokkan data siswa.</p>
                <a href="proses.php" class="btn btn-primary-custom"><i class="fas fa-play me-1"></i> Mulai Proses K-Means</a>
            </div>
        </div>
    <?php else: ?>
        <!-- Metric Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card border-start border-4 border-danger">
                    <div class="metric-info">
                        <h3><?= $countC1; ?> Siswa</h3>
                        <p>Total Cluster C1</p>
                    </div>
                    <div class="icon-wrapper text-danger bg-danger-subtle">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="metric-card border-start border-4 border-success">
                    <div class="metric-info">
                        <h3><?= $countC2; ?> Siswa</h3>
                        <p>Total Cluster C2</p>
                    </div>
                    <div class="icon-wrapper text-success bg-success-subtle">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card border-start border-4 border-warning">
                    <div class="metric-info">
                        <h3><?= $countC3; ?> Siswa</h3>
                        <p>Total Cluster C3</p>
                    </div>
                    <div class="icon-wrapper text-warning bg-warning-subtle">
                        <i class="fas fa-user-friends"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Sisi Kiri: Tabel Detail Hasil -->
            <div class="col-lg-8 mb-4">
                <div class="card card-custom">
                    <div class="card-header"><i class="fas fa-list me-1"></i> Detail Anggota Cluster</div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 530px; overflow-y: auto;">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="50">No</th>
                                        <th>Nama Siswa</th>
                                        <th class="text-center">Cluster</th>
                                        <th class="text-center">Nilai (X<sub>1</sub>)</th>
                                        <th class="text-center">Hadir (X<sub>2</sub>)</th>
                                        <th class="text-center">Jarak ke Centroid Terpilih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($results as $row): ?>
                                        <?php
                                        // Cari jarak ke centroid yang terpilih
                                        $c = $row['cluster_label'];
                                        $dist = 0;
                                        if ($c === 'C1') $dist = $row['jarak_c1'];
                                        elseif ($c === 'C2') $dist = $row['jarak_c2'];
                                        elseif ($c === 'C3') $dist = $row['jarak_c3'];
                                        ?>
                                        <tr>
                                            <td class="text-center fw-medium"><?= $no++; ?></td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?= strtolower($c); ?> px-3 py-1.5 rounded-pill fw-bold"><?= $c; ?></span>
                                            </td>
                                            <td class="text-center"><?= number_format($row['nilai_akademik'], 1); ?></td>
                                            <td class="text-center"><?= number_format($row['kehadiran_siswa'], 1); ?>%</td>
                                            <td class="text-center font-monospace text-muted"><?= number_format($dist, 4); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sisi Kanan: Grafik & Karakteristik -->
            <div class="col-lg-4">
                <!-- Grafik Distribusi -->
                <div class="card card-custom mb-4">
                    <div class="card-header"><i class="fas fa-chart-bar me-1"></i> Grafik Distribusi Cluster</div>
                    <div class="card-body">
                        <canvas id="barChartCluster" height="250"></canvas>
                    </div>
                </div>

                <!-- Karakteristik Cluster -->
                <div class="card card-custom">
                    <div class="card-header"><i class="fas fa-info-circle me-1"></i> Karakteristik Rata-Rata Cluster</div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush rounded-0">
                            <!-- C1 Info -->
                            <div class="list-group-item p-3 border-bottom">
                                <h6 class="fw-bold text-danger"><span class="badge badge-c1 px-2 py-1 rounded-pill me-2">C1</span> Karakteristik</h6>
                                <table class="table table-sm table-borderless small mb-0 mt-2 text-muted">
                                    <tr><td>Nilai Rata-Rata:</td><td class="text-end fw-semibold text-dark"><?= number_format($avgC1['nilai'], 2); ?></td></tr>
                                    <tr><td>Persentase Kehadiran:</td><td class="text-end fw-semibold text-dark"><?= number_format($avgC1['hadir'], 2); ?>%</td></tr>
                                    <tr><td>Keaktifan Kelas:</td><td class="text-end fw-semibold text-dark"><?= getAveragedLabel($avgC1['aktif']); ?></td></tr>
                                    <tr><td>Dukungan Ortu:</td><td class="text-end fw-semibold text-dark"><?= getAveragedLabel($avgC1['dukung']); ?></td></tr>
                                </table>
                            </div>
                            <!-- C2 Info -->
                            <div class="list-group-item p-3 border-bottom">
                                <h6 class="fw-bold text-success"><span class="badge badge-c2 px-2 py-1 rounded-pill me-2">C2</span> Karakteristik</h6>
                                <table class="table table-sm table-borderless small mb-0 mt-2 text-muted">
                                    <tr><td>Nilai Rata-Rata:</td><td class="text-end fw-semibold text-dark"><?= number_format($avgC2['nilai'], 2); ?></td></tr>
                                    <tr><td>Persentase Kehadiran:</td><td class="text-end fw-semibold text-dark"><?= number_format($avgC2['hadir'], 2); ?>%</td></tr>
                                    <tr><td>Keaktifan Kelas:</td><td class="text-end fw-semibold text-dark"><?= getAveragedLabel($avgC2['aktif']); ?></td></tr>
                                    <tr><td>Dukungan Ortu:</td><td class="text-end fw-semibold text-dark"><?= getAveragedLabel($avgC2['dukung']); ?></td></tr>
                                </table>
                            </div>
                            <!-- C3 Info -->
                            <div class="list-group-item p-3">
                                <h6 class="fw-bold text-warning"><span class="badge badge-c3 px-2 py-1 rounded-pill me-2">C3</span> Karakteristik</h6>
                                <table class="table table-sm table-borderless small mb-0 mt-2 text-muted">
                                    <tr><td>Nilai Rata-Rata:</td><td class="text-end fw-semibold text-dark"><?= number_format($avgC3['nilai'], 2); ?></td></tr>
                                    <tr><td>Persentase Kehadiran:</td><td class="text-end fw-semibold text-dark"><?= number_format($avgC3['hadir'], 2); ?>%</td></tr>
                                    <tr><td>Keaktifan Kelas:</td><td class="text-end fw-semibold text-dark"><?= getAveragedLabel($avgC3['aktif']); ?></td></tr>
                                    <tr><td>Dukungan Ortu:</td><td class="text-end fw-semibold text-dark"><?= getAveragedLabel($avgC3['dukung']); ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Render Grafik dengan Chart.js -->
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('barChartCluster').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Cluster C1', 'Cluster C2', 'Cluster C3'],
                    datasets: [{
                        label: 'Jumlah Siswa',
                        data: [<?= $countC1; ?>, <?= $countC2; ?>, <?= $countC3; ?>],
                        backgroundColor: [
                            'rgba(239, 68, 68, 0.8)',   // Merah (C1)
                            'rgba(34, 197, 94, 0.8)',   // Hijau (C2)
                            'rgba(234, 179, 8, 0.8)'    // Kuning (C3)
                        ],
                        borderColor: [
                            '#ef4444',
                            '#22c55e',
                            '#eab308'
                        ],
                        borderWidth: 1.5,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
        </script>
    <?php endif; ?>
</div>
<?php
require_once '../partials/footer.php';
?>
