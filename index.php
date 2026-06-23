<?php
// index.php (Dashboard Utama)

$page_title = "Dashboard";
require_once 'config/database.php';
require_once 'partials/header.php';

// Ambil total data siswa
$stmtSiswa = $pdo->query("SELECT COUNT(*) FROM tabel_siswa");
$totalSiswa = $stmtSiswa->fetchColumn();

// Ambil total hasil terklasifikasi
$stmtClustered = $pdo->query("SELECT COUNT(*) FROM tabel_hasil_clustering");
$totalClustered = $stmtClustered->fetchColumn();

$countC1 = 0;
$countC2 = 0;
$countC3 = 0;
$has_results = ($totalClustered > 0);

if ($has_results) {
    // Ambil distribusi data per cluster
    $stmtC1 = $pdo->query("SELECT COUNT(*) FROM tabel_hasil_clustering WHERE cluster_label = 'C1'");
    $countC1 = $stmtC1->fetchColumn();
    
    $stmtC2 = $pdo->query("SELECT COUNT(*) FROM tabel_hasil_clustering WHERE cluster_label = 'C2'");
    $countC2 = $stmtC2->fetchColumn();
    
    $stmtC3 = $pdo->query("SELECT COUNT(*) FROM tabel_hasil_clustering WHERE cluster_label = 'C3'");
    $countC3 = $stmtC3->fetchColumn();
    
    // Ambil 10 siswa terbaru dengan label clusternya
    $stmtRecent = $pdo->query("SELECT s.*, hc.cluster_label 
                               FROM tabel_siswa s 
                               INNER JOIN tabel_hasil_clustering hc ON s.id_siswa = hc.id_siswa 
                               ORDER BY s.id_siswa DESC LIMIT 10");
    $recentSiswa = $stmtRecent->fetchAll();
} else {
    // Ambil 10 data siswa terbaru (karena belum dicluster)
    $stmtRecent = $pdo->query("SELECT s.*, NULL as cluster_label 
                               FROM tabel_siswa s 
                               ORDER BY s.id_siswa DESC LIMIT 10");
    $recentSiswa = $stmtRecent->fetchAll();
}
?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="text-primary fw-bold mb-1"><i class="fas fa-home me-2"></i>Dashboard</h4>
            <p class="text-muted small">Panel navigasi utama sistem analisis K-Means clustering nilai siswa SDN 105361.</p>
        </div>
        <div class="col-md-6 text-md-end no-print">
            <span class="badge bg-light text-primary border px-3 py-2 small fw-semibold">
                <i class="far fa-clock me-1"></i> Hari Ini: <?= date('d M Y'); ?>
            </span>
        </div>
    </div>

    <!-- Ringkasan Kartu Statistik (Metrics) -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="metric-card border-start border-4 border-primary">
                <div class="metric-info">
                    <h3><?= $totalSiswa; ?> Siswa</h3>
                    <p>Total Data Siswa</p>
                </div>
                <div class="icon-wrapper">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="metric-card border-start border-4 border-success">
                <div class="metric-info">
                    <h3><?= $totalClustered; ?> Siswa</h3>
                    <p>Terklasifikasi</p>
                </div>
                <div class="icon-wrapper text-success bg-success-subtle">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="metric-card border-start border-4 border-info">
                <div class="metric-info">
                    <h3>3 Kelompok</h3>
                    <p>Target Cluster (K=3)</p>
                </div>
                <div class="icon-wrapper text-info bg-info-subtle">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Area Utama (Kiri/Bawah): Tabel Siswa Terakhir -->
        <div class="col-lg-8 mb-4">
            <div class="card card-custom h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-history me-1"></i> Data Siswa Terbaru</span>
                    <a href="siswa/index.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 no-print">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Nilai (X<sub>1</sub>)</th>
                                    <th class="text-center">Kehadiran (X<sub>2</sub>)</th>
                                    <th class="text-center">Cluster</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentSiswa)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-info-circle me-1"></i> Belum ada data siswa. Silakan tambahkan data.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($recentSiswa as $row): ?>
                                        <tr>
                                            <td class="text-center fw-medium"><?= $no++; ?></td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                            <td class="text-center"><?= number_format($row['nilai_akademik'], 1); ?></td>
                                            <td class="text-center"><?= number_format($row['kehadiran_siswa'], 1); ?>%</td>
                                            <td class="text-center">
                                                <?php if ($row['cluster_label']): ?>
                                                    <span class="badge badge-<?= strtolower($row['cluster_label']); ?> px-3 py-1.5 rounded-pill fw-bold"><?= $row['cluster_label']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill fw-semibold">- Belum diproses -</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Area Samping (Kanan): Chart atau Info Status -->
        <div class="col-lg-4 mb-4">
            <?php if (!$has_results): ?>
                <div class="card card-custom h-100 text-center py-5 px-4 d-flex flex-column justify-content-center align-items-center">
                    <div class="text-muted mb-4"><i class="fas fa-cogs fs-1 text-primary"></i></div>
                    <h5 class="fw-bold">Cluster Belum Diproses</h5>
                    <p class="text-muted small">Anda belum melakukan clustering pada data siswa aktif saat ini.</p>
                    <a href="clustering/proses.php" class="btn btn-primary-custom btn-sm mt-3"><i class="fas fa-play me-1"></i> Jalankan K-Means</a>
                </div>
            <?php else: ?>
                <div class="card card-custom h-100">
                    <div class="card-header"><i class="fas fa-chart-pie me-1"></i> Distribusi Persentase Cluster</div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div style="position: relative; height:240px; width:240px;">
                            <canvas id="donutChartCluster"></canvas>
                        </div>
                        <div class="mt-4 w-100">
                            <div class="d-flex justify-content-between border-bottom py-2 small">
                                <span class="fw-semibold text-danger"><i class="fas fa-circle me-1"></i> Cluster C1</span>
                                <span class="fw-bold"><?= $countC1; ?> Siswa</span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-2 small">
                                <span class="fw-semibold text-success"><i class="fas fa-circle me-1"></i> Cluster C2</span>
                                <span class="fw-bold"><?= $countC2; ?> Siswa</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 small">
                                <span class="fw-semibold text-warning"><i class="fas fa-circle me-1"></i> Cluster C3</span>
                                <span class="fw-bold"><?= $countC3; ?> Siswa</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($has_results): ?>
    <!-- JavaScript Chart rendering -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('donutChartCluster').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Cluster C1', 'Cluster C2', 'Cluster C3'],
                datasets: [{
                    data: [<?= $countC1; ?>, <?= $countC2; ?>, <?= $countC3; ?>],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(234, 179, 8, 0.8)'
                    ],
                    borderColor: [
                        '#ffffff',
                        '#ffffff',
                        '#ffffff'
                    ],
                    borderWidth: 2
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
                cutout: '65%'
            }
        });
    });
    </script>
<?php endif; ?>

<?php
require_once 'partials/footer.php';
?>
