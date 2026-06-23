<?php
// clustering/cetak.php

$page_title = "Cetak Laporan";
require_once '../config/database.php';
require_once '../partials/header.php';

// Ambil data hasil clustering beserta detail siswa
$query = "SELECT s.*, hc.cluster_label, hc.jarak_c1, hc.jarak_c2, hc.jarak_c3 
          FROM tabel_siswa s 
          INNER JOIN tabel_hasil_clustering hc ON s.id_siswa = hc.id_siswa
          ORDER BY hc.cluster_label ASC, s.nama_siswa ASC";
$stmt = $pdo->query($query);
$results = $stmt->fetchAll();

if (empty($results)) {
    $_SESSION['error'] = 'Hasil clustering belum tersedia. Silakan jalankan proses clustering terlebih dahulu!';
    header("Location: hasil.php");
    exit();
}

$countC1 = 0;
$countC2 = 0;
$countC3 = 0;

foreach ($results as $row) {
    if ($row['cluster_label'] === 'C1') $countC1++;
    elseif ($row['cluster_label'] === 'C2') $countC2++;
    elseif ($row['cluster_label'] === 'C3') $countC3++;
}
?>
<div class="container-fluid">
    <!-- Panel Kontrol (no-print) -->
    <div class="row mb-4 no-print">
        <div class="col-md-6">
            <h4 class="text-primary fw-bold mb-1"><i class="fas fa-print me-2"></i>Cetak Laporan Hasil Analisis</h4>
            <p class="text-muted small">Pratinjau laporan cetak. Gunakan tombol di sebelah kanan untuk mulai mencetak dokumen.</p>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-md-end gap-2">
            <a href="hasil.php" class="btn btn-secondary-custom btn-sm"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            <button onclick="window.print()" class="btn btn-primary-custom btn-sm"><i class="fas fa-print me-1"></i> Cetak Laporan (PDF)</button>
        </div>
    </div>

    <!-- Area Dokumen Cetak (Print Container) -->
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card card-custom p-5 bg-white border-0 shadow-sm" style="min-height: 29.7cm; padding: 2cm !important;">
                
                <!-- Kop Surat / Letterhead -->
                <div class="text-center border-bottom border-3 border-dark pb-3 mb-4">
                    <h5 class="fw-bold mb-1 text-uppercase">Pemerintah Kabupaten Serdang Bedagai</h5>
                    <h5 class="fw-bold mb-1 text-uppercase">Dinas Pendidikan</h5>
                    <h4 class="fw-bold mb-1 text-uppercase">SD NEGERI 105361 LUBUK CEMARA</h4>
                    <p class="text-muted small mb-0">Kec. Perbaungan, Kab. Serdang Bedagai, Sumatera Utara</p>
                </div>

                <!-- Judul Laporan -->
                <div class="text-center mb-4">
                    <h5 class="fw-bold text-decoration-underline text-uppercase">Laporan Hasil Clustering Nilai Akademik Siswa</h5>
                    <p class="text-muted small">Tanggal Cetak: <?= date('d F Y'); ?></p>
                </div>

                <!-- Informasi Ringkasan Dokumen -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2">I. RINGKASAN ANALISIS</h6>
                    <p class="small text-dark text-justify">
                        Berdasarkan hasil pemrosesan data mining menggunakan algoritma K-Means Clustering terhadap data nilai siswa SDN 105361 Lubuk Cemara dengan total sampel sebanyak <strong><?= count($results); ?> siswa</strong>, diperoleh hasil pengelompokan (cluster) dengan distribusi sebagai berikut:
                    </p>
                    <table class="table table-bordered table-sm small text-center w-75 mx-auto my-3">
                        <thead class="table-light">
                            <tr>
                                <th>Cluster Label</th>
                                <th>Deskripsi Kategori</th>
                                <th>Jumlah Siswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">C1</td>
                                <td>Cluster Kelompok 1</td>
                                <td><?= $countC1; ?> Siswa</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">C2</td>
                                <td>Cluster Kelompok 2</td>
                                <td><?= $countC2; ?> Siswa</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">C3</td>
                                <td>Cluster Kelompok 3</td>
                                <td><?= $countC3; ?> Siswa</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tabel Lampiran Hasil Detail -->
                <div class="mb-5">
                    <h6 class="fw-bold text-dark mb-2">II. DETAIL DISTRIBUSI SISWA PER CLUSTER</h6>
                    <table class="table table-bordered table-sm small text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="40">No</th>
                                <th class="text-start">Nama Siswa</th>
                                <th>Nilai Rata-Rata (X<sub>1</sub>)</th>
                                <th>Kehadiran (X<sub>2</sub>)</th>
                                <th>Keaktifan (X<sub>3</sub>)</th>
                                <th>Dukungan Ortu (X<sub>4</sub>)</th>
                                <th>Cluster</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($results as $row): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td class="text-start"><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                    <td><?= number_format($row['nilai_akademik'], 1); ?></td>
                                    <td><?= number_format($row['kehadiran_siswa'], 1); ?>%</td>
                                    <td>
                                        <?php
                                        if ($row['keaktifan_siswa'] == 3) echo 'Sangat Aktif';
                                        elseif ($row['keaktifan_siswa'] == 2) echo 'Aktif';
                                        else echo 'Kurang Aktif';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($row['dukungan_ortu'] == 3) echo 'Sangat Didukung';
                                        elseif ($row['dukungan_ortu'] == 2) echo 'Didukung';
                                        else echo 'Kurang Didukung';
                                        ?>
                                    </td>
                                    <td class="fw-bold"><?= $row['cluster_label']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Tanda Tangan Kepala Sekolah -->
                <div class="row mt-auto">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="small mb-0">Lubuk Cemara, <?= date('d F Y'); ?></p>
                        <p class="small fw-semibold mb-5">Kepala Sekolah SDN 105361,</p>
                        <br/><br/>
                        <p class="small fw-bold text-decoration-underline mb-0">Rinan Iskandar Zein, S. Pd.</p>
                        <p class="small text-muted mb-0">NIP. 19700101 199503 1 001</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php
require_once '../partials/footer.php';
?>
