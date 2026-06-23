<?php
// config/database.php

$host = '127.0.0.1';
$db   = 'db_kmeans_riza';
$user = 'root';
$pass = ''; // Default password untuk XAMPP di Windows/Mac biasanya kosong
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan kesalahan profesional
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
