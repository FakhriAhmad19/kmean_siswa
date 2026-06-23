<?php
// helpers/kmeans.php

class KMeans {
    private $pdo;
    private $k;
    private $students = [];
    private $minMax = [];
    private $normalizedData = [];
    
    public function __construct($pdo, $k = 3) {
        $this->pdo = $pdo;
        $this->k = $k;
        $this->loadStudents();
        $this->calculateMinMax();
        $this->normalizeData();
    }
    
    private function loadStudents() {
        $stmt = $this->pdo->query("SELECT * FROM tabel_siswa ORDER BY id_siswa ASC");
        $this->students = $stmt->fetchAll();
    }
    
    private function calculateMinMax() {
        if (empty($this->students)) return;
        
        // Nilai min-max dinamis dari database
        $this->minMax = [
            'nilai' => ['min' => 100.0, 'max' => 0.0],
            'kehadiran' => ['min' => 100.0, 'max' => 0.0],
            'keaktifan' => ['min' => 3, 'max' => 1], // Tetap berdasarkan skala 1 s.d 3
            'dukungan' => ['min' => 3, 'max' => 1]   // Tetap berdasarkan skala 1 s.d 3
        ];
        
        foreach ($this->students as $s) {
            if ($s['nilai_akademik'] < $this->minMax['nilai']['min']) $this->minMax['nilai']['min'] = $s['nilai_akademik'];
            if ($s['nilai_akademik'] > $this->minMax['nilai']['max']) $this->minMax['nilai']['max'] = $s['nilai_akademik'];
            
            if ($s['kehadiran_siswa'] < $this->minMax['kehadiran']['min']) $this->minMax['kehadiran']['min'] = $s['kehadiran_siswa'];
            if ($s['kehadiran_siswa'] > $this->minMax['kehadiran']['max']) $this->minMax['kehadiran']['max'] = $s['kehadiran_siswa'];
        }
        
        // Cegah pembagian dengan nol
        foreach ($this->minMax as $key => $val) {
            if ($val['max'] == $val['min']) {
                $this->minMax[$key]['max'] += 0.0001;
            }
        }
    }
    
    private function normalizeData() {
        $this->normalizedData = [];
        foreach ($this->students as $s) {
            $norm_nilai = ($s['nilai_akademik'] - $this->minMax['nilai']['min']) / ($this->minMax['nilai']['max'] - $this->minMax['nilai']['min']);
            $norm_kehadiran = ($s['kehadiran_siswa'] - $this->minMax['kehadiran']['min']) / ($this->minMax['kehadiran']['max'] - $this->minMax['kehadiran']['min']);
            $norm_keaktifan = ($s['keaktifan_siswa'] - $this->minMax['keaktifan']['min']) / ($this->minMax['keaktifan']['max'] - $this->minMax['keaktifan']['min']);
            $norm_dukungan = ($s['dukungan_ortu'] - $this->minMax['dukungan']['min']) / ($this->minMax['dukungan']['max'] - $this->minMax['dukungan']['min']);
            
            $this->normalizedData[$s['id_siswa']] = [
                'id_siswa' => $s['id_siswa'],
                'nama_siswa' => $s['nama_siswa'],
                'x1' => $norm_nilai,
                'x2' => $norm_kehadiran,
                'x3' => $norm_keaktifan,
                'x4' => $norm_dukungan,
                'original' => $s
            ];
        }
    }
    
    public function getMinMax() {
        return $this->minMax;
    }
    
    public function getNormalizedData() {
        return $this->normalizedData;
    }
    
    // Perhitungan Jarak Euclidean
    private function euclideanDistance($p1, $p2) {
        return sqrt(
            pow($p1['x1'] - $p2['x1'], 2) +
            pow($p1['x2'] - $p2['x2'], 2) +
            pow($p1['x3'] - $p2['x3'], 2) +
            pow($p1['x4'] - $p2['x4'], 2)
        );
    }
    
    // Jalankan K-Means dan kumpulkan riwayat iterasi
    public function run($initialCentroids = null) {
        if (empty($this->normalizedData)) {
            return [];
        }
        
        // 1. Tentukan Centroid Awal
        $centroids = [];
        if ($initialCentroids && count($initialCentroids) == $this->k) {
            $centroids = $initialCentroids;
        } else {
            // Ambil acak data awal dari database
            $randomKeys = array_rand($this->normalizedData, $this->k);
            if (!is_array($randomKeys)) {
                $randomKeys = [$randomKeys];
            }
            $i = 1;
            foreach ($randomKeys as $key) {
                $centroids['C' . $i] = [
                    'x1' => $this->normalizedData[$key]['x1'],
                    'x2' => $this->normalizedData[$key]['x2'],
                    'x3' => $this->normalizedData[$key]['x3'],
                    'x4' => $this->normalizedData[$key]['x4'],
                ];
                $i++;
            }
        }
        
        $history = [];
        $converged = false;
        $iteration = 1;
        $maxIterations = 50;
        
        while (!$converged && $iteration <= $maxIterations) {
            $assignments = [];
            $clusterSum = [];
            
            // Inisialisasi penjumlahan koordinat cluster
            for ($i = 1; $i <= $this->k; $i++) {
                $clusterSum['C' . $i] = ['x1' => 0.0, 'x2' => 0.0, 'x3' => 0.0, 'x4' => 0.0, 'count' => 0];
            }
            
            // 2. Assignment: Hitung jarak Euclidean dan tentukan cluster terdekat
            foreach ($this->normalizedData as $id => $data) {
                $minDist = INF;
                $assignedCluster = '';
                $distances = [];
                
                for ($i = 1; $i <= $this->k; $i++) {
                    $cLabel = 'C' . $i;
                    $dist = $this->euclideanDistance($data, $centroids[$cLabel]);
                    $distances[$cLabel] = $dist;
                    
                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $assignedCluster = $cLabel;
                    }
                }
                
                $assignments[$id] = [
                    'id_siswa' => $id,
                    'nama_siswa' => $data['nama_siswa'],
                    'distances' => $distances,
                    'cluster' => $assignedCluster,
                    'x1' => $data['x1'],
                    'x2' => $data['x2'],
                    'x3' => $data['x3'],
                    'x4' => $data['x4'],
                ];
                
                // Tambahkan data ke cluster penjumlahan
                $clusterSum[$assignedCluster]['x1'] += $data['x1'];
                $clusterSum[$assignedCluster]['x2'] += $data['x2'];
                $clusterSum[$assignedCluster]['x3'] += $data['x3'];
                $clusterSum[$assignedCluster]['x4'] += $data['x4'];
                $clusterSum[$assignedCluster]['count']++;
            }
            
            // 3. Update Centroid: Hitung rata-rata koordinat baru
            $newCentroids = [];
            for ($i = 1; $i <= $this->k; $i++) {
                $cLabel = 'C' . $i;
                $count = $clusterSum[$cLabel]['count'];
                
                if ($count > 0) {
                    $newCentroids[$cLabel] = [
                        'x1' => $clusterSum[$cLabel]['x1'] / $count,
                        'x2' => $clusterSum[$cLabel]['x2'] / $count,
                        'x3' => $clusterSum[$cLabel]['x3'] / $count,
                        'x4' => $clusterSum[$cLabel]['x4'] / $count,
                    ];
                } else {
                    $newCentroids[$cLabel] = $centroids[$cLabel];
                }
            }
            
            // Simpan log iterasi saat ini
            $history[$iteration] = [
                'iteration' => $iteration,
                'centroids' => $centroids,
                'assignments' => $assignments
            ];
            
            // 4. Cek Konvergensi: Apakah koordinat centroid bergeser?
            $changed = false;
            for ($i = 1; $i <= $this->k; $i++) {
                $cLabel = 'C' . $i;
                if (
                    abs($newCentroids[$cLabel]['x1'] - $centroids[$cLabel]['x1']) > 0.0001 ||
                    abs($newCentroids[$cLabel]['x2'] - $centroids[$cLabel]['x2']) > 0.0001 ||
                    abs($newCentroids[$cLabel]['x3'] - $centroids[$cLabel]['x3']) > 0.0001 ||
                    abs($newCentroids[$cLabel]['x4'] - $centroids[$cLabel]['x4']) > 0.0001
                ) {
                    $changed = true;
                    break;
                }
            }
            
            if (!$changed) {
                $converged = true;
            } else {
                $centroids = $newCentroids;
                $iteration++;
            }
        }
        
        return $history;
    }
    
    // Simpan hasil clustering akhir ke database
    public function saveResults($finalAssignments) {
        $this->pdo->query("TRUNCATE TABLE tabel_hasil_clustering");
        
        $stmt = $this->pdo->prepare("INSERT INTO tabel_hasil_clustering (id_siswa, cluster_label, jarak_c1, jarak_c2, jarak_c3) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($finalAssignments as $id => $assign) {
            $stmt->execute([
                $id,
                $assign['cluster'],
                $assign['distances']['C1'],
                $assign['distances']['C2'],
                $assign['distances']['C3'] ?? 0.0
            ]);
        }
    }
}
?>
