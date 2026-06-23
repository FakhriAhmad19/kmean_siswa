<?php
// siswa/hapus.php

session_start();

// Cek autentikasi admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../config/database.php';

$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;

if ($id > 0) {
    try {
        // Hapus data siswa (akan menghapus hasil clustering karena foreign key ON DELETE CASCADE)
        $stmt = $pdo->prepare("DELETE FROM tabel_siswa WHERE id_siswa = ?");
        $stmt->execute([$id]);
        
        // Hapus seluruh tabel hasil clustering untuk konsistensi model (agar di-cluster ulang)
        $pdo->query("TRUNCATE TABLE tabel_hasil_clustering");
        
        $_SESSION['success'] = 'Data siswa berhasil dihapus. Hasil clustering sebelumnya telah di-reset untuk pemrosesan ulang.';
    } catch (\PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus data siswa: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'ID siswa tidak valid!';
}

header("Location: index.php");
exit();
?>
