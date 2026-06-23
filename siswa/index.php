<?php
// siswa/index.php

$page_title = "Data Siswa";
require_once '../config/database.php';
require_once '../partials/header.php';

// Menangani pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$query = "SELECT s.*, hc.cluster_label 
          FROM tabel_siswa s 
          LEFT JOIN tabel_hasil_clustering hc ON s.id_siswa = hc.id_siswa";

if ($search !== '') {
    $query .= " WHERE s.nama_siswa LIKE :search";
}
$query .= " ORDER BY s.id_siswa DESC";

$stmt = $pdo->prepare($query);
if ($search !== '') {
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt->execute();
}
$siswa_list = $stmt->fetchAll();

// Fungsi helper untuk merender teks kategori keaktifan
function getKeaktifanText($val) {
    switch ($val) {
        case 3: return 'Sangat Aktif';
        case 2: return 'Aktif';
        case 1: return 'Kurang Aktif';
        default: return 'Tidak Diketahui';
    }
}

// Fungsi helper untuk merender teks kategori dukungan orang tua
function getDukunganText($val) {
    switch ($val) {
        case 3: return 'Sangat Didukung';
        case 2: return 'Didukung';
        case 1: return 'Kurang Didukung';
        default: return 'Tidak Diketahui';
    }
}
?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="text-primary fw-bold mb-1"><i class="fas fa-graduation-cap me-2"></i>Data Siswa</h4>
            <p class="text-muted small">Kelola data nilai siswa SDN 105361 Lubuk Cemara untuk analisis clustering.</p>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-md-end gap-2 no-print">
            <form action="" method="GET" class="d-flex me-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm border-blue" placeholder="Cari nama siswa..." value="<?= htmlspecialchars($search); ?>">
                    <button class="btn btn-primary-custom btn-sm" type="submit"><i class="fas fa-search"></i></button>
                    <?php if ($search !== ''): ?>
                        <a href="index.php" class="btn btn-secondary-custom btn-sm"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                </div>
            </form>
            <a href="tambah.php" class="btn btn-primary-custom btn-sm"><i class="fas fa-plus me-1"></i> Tambah Data</a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success py-2 px-3 small border-0 rounded-3 mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <div><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger py-2 px-3 small border-0 rounded-3 mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <div><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        </div>
    <?php endif; ?>

    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">Nilai Rata-Rata (X<sub>1</sub>)</th>
                            <th class="text-center">Kehadiran (X<sub>2</sub>)</th>
                            <th class="text-center">Keaktifan (X<sub>3</sub>)</th>
                            <th class="text-center">Dukungan Orang Tua (X<sub>4</sub>)</th>
                            <th class="text-center">Hasil Cluster</th>
                            <th class="text-center no-print" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($siswa_list)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Tidak ada data siswa yang ditemukan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($siswa_list as $row): ?>
                                <tr>
                                    <td class="text-center fw-medium"><?= $no++; ?></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                    <td class="text-center fw-medium"><?= number_format($row['nilai_akademik'], 1); ?></td>
                                    <td class="text-center fw-medium"><?= number_format($row['kehadiran_siswa'], 1); ?>%</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1 small"><?= getKeaktifanText($row['keaktifan_siswa']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1 small"><?= getDukunganText($row['dukungan_ortu']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['cluster_label']): ?>
                                            <span class="badge badge-<?= strtolower($row['cluster_label']); ?> px-3 py-1.5 rounded-pill fw-bold"><?= $row['cluster_label']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary text-white px-3 py-1.5 rounded-pill fw-semibold">- Belum diproses -</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center no-print">
                                        <a href="edit.php?id=<?= $row['id_siswa']; ?>" class="btn btn-sm btn-outline-primary me-1 py-1 px-2" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="hapus.php?id=<?= $row['id_siswa']; ?>" class="btn btn-sm btn-outline-danger py-1 px-2" onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')" title="Hapus Data">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
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
<?php
require_once '../partials/footer.php';
?>
