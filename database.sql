-- Hapus database jika sudah ada
DROP DATABASE IF EXISTS db_kmeans_riza;

-- Buat database baru
CREATE DATABASE db_kmeans_riza;
USE db_kmeans_riza;

-- 1. Struktur Tabel Pengguna (Admin)
CREATE TABLE tabel_pengguna (
    id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Seed data admin (password: admin, di-hash dengan bcrypt)
INSERT INTO tabel_pengguna (username, password) VALUES 
('admin', '$2y$12$3jr.nWHeCU.rkwdj.Zc65uqCgAnLBGB111T6WpyvUj//kFRzgf.7S');

-- 2. Struktur Tabel Siswa
CREATE TABLE tabel_siswa (
    id_siswa INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_siswa VARCHAR(100) NOT NULL,
    nilai_akademik FLOAT NOT NULL,
    kehadiran_siswa FLOAT NOT NULL,
    keaktifan_siswa INT(2) NOT NULL, -- 1=Kurang Aktif, 2=Aktif, 3=Sangat Aktif
    dukungan_ortu INT(2) NOT NULL    -- 1=Kurang Didukung, 2=Didukung, 3=Sangat Didukung
);

-- Seed data 60 siswa dari proposal
INSERT INTO tabel_siswa (nama_siswa, nilai_akademik, kehadiran_siswa, keaktifan_siswa, dukungan_ortu) VALUES
('Aidana Khaira Fitri', 88, 93, 2, 3),
('Aisya Turahmah', 84, 80, 2, 3),
('Alvino Diandra', 80, 79, 1, 3),
('Arya Pradana', 85, 86, 2, 1),
('Az Zahra Syakira Ramadhani', 86, 75, 3, 3),
('Azka Rezky Alkhairi', 83, 90, 1, 2),
('Dhika Ramadhan', 83, 75, 2, 3),
('Difiana Shavira', 83, 93, 2, 3),
('Fiqri Nakhla Ar Rasyid', 87, 86, 3, 1),
('Hadiba Shakila', 80, 89, 3, 3),
('Kira Ramadhani Hafidzah', 84, 83, 3, 3),
('Muhammad Alfarizi Syahna', 81, 83, 1, 2),
('Muhammad Azka Pratama', 79, 98, 3, 2),
('Muhammad Fauzan Rafatar', 81, 85, 2, 2),
('Muhammad Maulanik', 81, 77, 3, 3),
('Muhammad Rizky', 83, 78, 1, 2),
('Muhammad Safwan Hamidi', 80, 81, 3, 3),
('Muhammad Zaid Al Hafiz Hsb', 82, 98, 3, 2),
('Mutiara Dewi', 85, 81, 3, 3),
('Natasya Aprilia', 81, 76, 2, 3),
('Naura Aqila', 88, 81, 3, 2),
('Naura Azalea', 81, 82, 3, 2),
('Noor Fauzan Azmi', 87, 84, 1, 1),
('Putri Annisa', 82, 95, 3, 1),
('Qurratu\'Ain Naziya', 88, 85, 2, 2),
('Rahma Dhani Bilqis', 82, 97, 1, 1),
('Sabrina Saki', 85, 84, 2, 1),
('Sahci Fabriziah', 84, 77, 3, 2),
('Siti Bahar', 82, 84, 1, 3),
('Thoriq Iskandar Muda', 86, 87, 1, 3),
('Widima Okta Damanik', 80, 79, 3, 2),
('Abdi Pragusti', 82, 77, 1, 3),
('Ahmad Zaky Al-Fatih', 83, 84, 1, 2),
('Aisyah Nuha Zahira', 87, 84, 1, 2),
('Alfarezi Santika', 86, 91, 1, 3),
('Aqila Khumairoh', 85, 81, 1, 2),
('Ariska Shasfa', 88, 95, 3, 1),
('Bilqis Aqilah Ramadani', 74, 85, 2, 1),
('Cindy Azzahra', 87, 76, 2, 1),
('Dewa Syahputra', 75, 91, 1, 1),
('Dila Oktavia', 83, 76, 2, 3),
('Dwi Zahraini', 69, 90, 2, 2),
('Hafizah Putri Kirana', 78, 82, 1, 1),
('Hanna Mawaddah', 74, 77, 1, 3),
('Ikhwatun Najira', 82, 98, 3, 3),
('Khanaya Anggita', 80, 96, 1, 3),
('Marwan Soleh', 77, 98, 1, 2),
('Muhammad Fachri Ramadhan', 84, 79, 1, 3),
('Muhammad Imam Al Hasan', 72, 78, 2, 1),
('Muhammad Luthfi', 71, 93, 3, 3),
('Muhammad Zidan Alfiqi', 73, 88, 3, 2),
('Muhammmad Reyfan', 83, 97, 1, 3),
('Muhammmad Zikri', 74, 89, 3, 3),
('Nabila Nur Wahyudi', 87, 91, 2, 1),
('Natasya', 82, 78, 1, 1),
('Nur Aisyah Ramayani', 80, 77, 1, 1),
('Qori Al Habsy', 78, 77, 2, 2),
('Rafif Arka Ahmad', 89, 86, 2, 2),
('Rasya Putra Arla', 74, 91, 1, 3),
('Rifqi Al Hapsi', 73, 80, 2, 1);

-- 3. Struktur Tabel Hasil Clustering
CREATE TABLE tabel_hasil_clustering (
    id_hasil INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_siswa INT(11) NOT NULL,
    cluster_label VARCHAR(10) NOT NULL, -- C1, C2, atau C3
    jarak_c1 FLOAT NOT NULL,
    jarak_c2 FLOAT NOT NULL,
    jarak_c3 FLOAT NOT NULL,
    FOREIGN KEY (id_siswa) REFERENCES tabel_siswa(id_siswa) ON DELETE CASCADE
);
