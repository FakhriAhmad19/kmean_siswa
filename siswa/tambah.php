<?php
// siswa/tambah.php

$page_title = "Tambah Siswa";
require_once '../config/database.php';
require_once '../partials/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_siswa']);
    $nilai = filter_var($_POST['nilai_akademik'], FILTER_VALIDATE_FLOAT);
    $kehadiran = filter_var($_POST['kehadiran_siswa'], FILTER_VALIDATE_FLOAT);
    $keaktifan = filter_var($_POST['keaktifan_siswa'], FILTER_VALIDATE_INT);
    $dukungan = filter_var($_POST['dukungan_ortu'], FILTER_VALIDATE_INT);

    if (empty($nama) || $nilai === false || $kehadiran === false || $keaktifan === false || $dukungan === false) {
        $error = 'Semua field wajib diisi dengan format yang benar!';
    } elseif ($nilai < 0 || $nilai > 100 || $kehadiran < 0 || $kehadiran > 100) {
        $error = 'Nilai Akademik dan Kehadiran harus berada dalam rentang 0 s.d 100!';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tabel_siswa (nama_siswa, nilai_akademik, kehadiran_siswa, keaktifan_siswa, dukungan_ortu) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $nilai, $kehadiran, $keaktifan, $dukungan]);
            
            // Hapus hasil clustering lama karena ada data baru (harus di-cluster ulang)
            $pdo->query("TRUNCATE TABLE tabel_hasil_clustering");
            
            $_SESSION['success'] = 'Data siswa berhasil ditambahkan. Hasil clustering sebelumnya telah di-reset untuk pemrosesan ulang.';
            header("Location: index.php");
            exit();
        } catch (\PDOException $e) {
            $error = 'Terjadi kesalahan database: ' . $e->getMessage();
        }
    }
}
?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="text-primary fw-bold mb-1"><i class="fas fa-plus me-2"></i>Tambah Data Siswa</h4>
            <p class="text-muted small">Tambahkan data siswa baru beserta nilai indikatornya.</p>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-md-end no-print">
            <a href="index.php" class="btn btn-secondary-custom btn-sm"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 px-3 small border-0 rounded-3 mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-exclamation-circle"></i>
            <div><?= $error; ?></div>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-custom">
                <div class="card-header">
                    Form Data Siswa
                </div>
                <div class="card-body">
                    <form action="" method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label for="nama_siswa" class="form-label small fw-semibold text-muted">Nama Siswa</label>
                            <input type="text" name="nama_siswa" id="nama_siswa" class="form-control border-blue" placeholder="Nama lengkap siswa" required value="<?= htmlspecialchars($nama ?? ''); ?>">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nilai_akademik" class="form-label small fw-semibold text-muted">Nilai Rata-Rata Akademik (X<sub>1</sub>)</label>
                                <input type="number" name="nilai_akademik" id="nilai_akademik" class="form-control border-blue" placeholder="Contoh: 85.5" required step="0.1" min="0" max="100" value="<?= htmlspecialchars($nilai ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="kehadiran_siswa" class="form-label small fw-semibold text-muted">Persentase Kehadiran (X<sub>2</sub>)</label>
                                <div class="input-group">
                                    <input type="number" name="kehadiran_siswa" id="kehadiran_siswa" class="form-control border-blue" placeholder="Contoh: 95.0" required step="0.1" min="0" max="100" value="<?= htmlspecialchars($kehadiran ?? ''); ?>">
                                    <span class="input-group-text bg-light border-blue text-muted">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="keaktifan_siswa" class="form-label small fw-semibold text-muted">Keaktifan Siswa (X<sub>3</sub>)</label>
                                <select name="keaktifan_siswa" id="keaktifan_siswa" class="form-select border-blue" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    <option value="1" <?= isset($keaktifan) && $keaktifan == 1 ? 'selected' : ''; ?>>Kurang Aktif</option>
                                    <option value="2" <?= isset($keaktifan) && $keaktifan == 2 ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="3" <?= isset($keaktifan) && $keaktifan == 3 ? 'selected' : ''; ?>>Sangat Aktif</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dukungan_ortu" class="form-label small fw-semibold text-muted">Dukungan Orang Tua (X<sub>4</sub>)</label>
                                <select name="dukungan_ortu" id="dukungan_ortu" class="form-select border-blue" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    <option value="1" <?= isset($dukungan) && $dukungan == 1 ? 'selected' : ''; ?>>Kurang Didukung</option>
                                    <option value="2" <?= isset($dukungan) && $dukungan == 2 ? 'selected' : ''; ?>>Didukung</option>
                                    <option value="3" <?= isset($dukungan) && $dukungan == 3 ? 'selected' : ''; ?>>Sangat Didukung</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 py-2">
                            <i class="fas fa-save me-1"></i> Simpan Data Siswa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../partials/footer.php';
?>
