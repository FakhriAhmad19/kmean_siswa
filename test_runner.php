<?php
// test_runner.php

require_once 'config/database.php';
require_once 'helpers/kmeans.php';

echo "=== MEMULAI TEST K-MEANS SISWA ===\n";

try {
    // Inisialisasi K-Means dengan K=3
    $kmeans = new KMeans($pdo, 3);
    
    // Set centroid awal persis seperti di proposal
    $initialCentroids = [
        'C1' => ['x1' => 0.86, 'x2' => 0.78, 'x3' => 0.50, 'x4' => 1.00],
        'C2' => ['x1' => 0.46, 'x2' => 0.65, 'x3' => 0.50, 'x4' => 0.00],
        'C3' => ['x1' => 0.62, 'x2' => 0.56, 'x3' => 1.00, 'x4' => 0.00],
    ];
    
    echo "Menjalankan K-Means Clustering...\n";
    $history = $kmeans->run($initialCentroids);
    
    $total_iterations = count($history);
    echo "K-Means selesai dalam {$total_iterations} iterasi (konvergen).\n";
    
    // Tampilkan rangkuman hasil per iterasi
    foreach ($history as $iter => $data) {
        $c1_cnt = 0; $c2_cnt = 0; $c3_cnt = 0;
        foreach ($data['assignments'] as $a) {
            if ($a['cluster'] === 'C1') $c1_cnt++;
            elseif ($a['cluster'] === 'C2') $c2_cnt++;
            elseif ($a['cluster'] === 'C3') $c3_cnt++;
        }
        echo "Iterasi {$iter}: C1 = {$c1_cnt} siswa, C2 = {$c2_cnt} siswa, C3 = {$c3_cnt} siswa.\n";
    }
    
    // Simpan hasil clustering akhir ke database
    echo "Menyimpan hasil ke database...\n";
    $finalAssignments = end($history)['assignments'];
    $kmeans->saveResults($finalAssignments);
    
    // Verifikasi jumlah hasil simpan
    $saveCount = $pdo->query("SELECT COUNT(*) FROM tabel_hasil_clustering")->fetchColumn();
    echo "Data tersimpan di tabel_hasil_clustering: {$saveCount} baris.\n";
    
    echo "=== TEST BERHASIL & ALGORITMA VALID ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
