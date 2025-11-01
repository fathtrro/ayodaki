<?php
 $host = "localhost";
 $user = "root";
 $pass = "";
 $dbname = "db_pendakian";

 $conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Hapus tabel yang ada jika perlu (untuk development)
// mysqli_query($conn, "DROP TABLE IF EXISTS denda");
// mysqli_query($conn, "DROP TABLE IF EXISTS konfirmasi_kedatangan");
// mysqli_query($conn, "DROP TABLE IF EXISTS pembayaran");
// mysqli_query($conn, "DROP TABLE IF EXISTS pendaki");
// mysqli_query($conn, "DROP TABLE IF EXISTS registrasi");
// mysqli_query($conn, "DROP TABLE IF EXISTS gunung");
// mysqli_query($conn, "DROP TABLE IF EXISTS lokasi");
// mysqli_query($conn, "DROP TABLE IF EXISTS users");

// Membuat tabel users
// mysqli_query($conn, "CREATE TABLE users (
//     id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
//     username VARCHAR(50) NOT NULL UNIQUE,
//     password VARCHAR(255) NOT NULL,
//     nama_lengkap VARCHAR(100) NOT NULL,
//     email VARCHAR(100) NOT NULL UNIQUE,
//     no_hp VARCHAR(20) NOT NULL,
//     role ENUM('user', 'admin') DEFAULT 'user',
//     foto_ktp VARCHAR(255) DEFAULT NULL,
//     status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
//     tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// )");

// Membuat tabel lokasi
// mysqli_query($conn, "CREATE TABLE lokasi (
//     id_lokasi INT(11) AUTO_INCREMENT PRIMARY KEY,
//     nama_lokasi VARCHAR(100) NOT NULL,
//     provinsi VARCHAR(50) NOT NULL,
//     koordinat VARCHAR(50) NOT NULL
// )");

// // Membuat tabel gunung
// mysqli_query($conn, "CREATE TABLE gunung (
//     id_gunung INT(11) AUTO_INCREMENT PRIMARY KEY,
//     nama_gunung VARCHAR(50) NOT NULL,
//     id_lokasi INT(11) NOT NULL,
//     ketinggian INT(11) NOT NULL,
//     tingkat_kesulitan VARCHAR(20) NOT NULL,
//     deskripsi TEXT NOT NULL,
//     syarat_ketentuan TEXT NOT NULL,
//     gambar VARCHAR(255) DEFAULT NULL,
//     FOREIGN KEY (id_lokasi) REFERENCES lokasi(id_lokasi)
// )");

// // Membuat tabel registrasi
// mysqli_query($conn, "CREATE TABLE registrasi (
//     id_registrasi INT(11) AUTO_INCREMENT PRIMARY KEY,
//     id_user INT(11) NOT NULL,
//     id_gunung INT(11) NOT NULL,
//     tanggal_pendakian DATE NOT NULL,
//     tanggal_selesai DATE NOT NULL,
//     no_hp VARCHAR(20) NOT NULL,
//     status_pembayaran VARCHAR(20) DEFAULT 'pending',
//     simaksi VARCHAR(20) DEFAULT NULL,
//     status_pendakian ENUM('pending', 'naik', 'selesai', 'batal') DEFAULT 'pending',
//     tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     FOREIGN KEY (id_user) REFERENCES users(id_user),
//     FOREIGN KEY (id_gunung) REFERENCES gunung(id_gunung)
// )");

// // Membuat tabel pendaki
// mysqli_query($conn, "CREATE TABLE pendaki (
//     id_pendaki INT(11) AUTO_INCREMENT PRIMARY KEY,
//     id_registrasi INT(11) NOT NULL,
//     nama VARCHAR(100) NOT NULL,
//     email VARCHAR(100) NOT NULL,
//     alamat TEXT NOT NULL,
//     FOREIGN KEY (id_registrasi) REFERENCES registrasi(id_registrasi)
// )");

// // Membuat tabel pembayaran
// mysqli_query($conn, "CREATE TABLE pembayaran (
//     id_pembayaran INT(11) AUTO_INCREMENT PRIMARY KEY,
//     id_registrasi INT(11) NOT NULL,
//     jumlah_bayar INT(11) NOT NULL,
//     metode_pembayaran VARCHAR(50) NOT NULL,
//     bukti_pembayaran VARCHAR(255) DEFAULT NULL,
//     status_pembayaran ENUM('pending', 'success', 'failed') DEFAULT 'pending',
//     tanggal_pembayaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     FOREIGN KEY (id_registrasi) REFERENCES registrasi(id_registrasi)
// )");

// // Membuat tabel konfirmasi_kedatangan
// mysqli_query($conn, "CREATE TABLE konfirmasi_kedatangan (
//     id_konfirmasi INT(11) AUTO_INCREMENT PRIMARY KEY,
//     id_registrasi INT(11) NOT NULL,
//     status_kedatangan ENUM('belum', 'sudah') DEFAULT 'belum',
//     tanggal_konfirmasi TIMESTAMP NULL,
//     keterangan TEXT,
//     FOREIGN KEY (id_registrasi) REFERENCES registrasi(id_registrasi)
// )");

// // Membuat tabel denda
// mysqli_query($conn, "CREATE TABLE denda (
//     id_denda INT(11) AUTO_INCREMENT PRIMARY KEY,
//     id_registrasi INT(11) NOT NULL,
//     jumlah_denda INT(11) NOT NULL,
//     alasan_denda VARCHAR(100) NOT NULL,
//     status_denda ENUM('pending', 'terbayar') DEFAULT 'pending',
//     tanggal_denda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     FOREIGN KEY (id_registrasi) REFERENCES registrasi(id_registrasi)
// )");

// // Menambahkan data awal
//  $result = mysqli_query($conn, "SELECT COUNT(*) FROM lokasi");
// if (mysqli_fetch_array($result)[0] == 0) {
//     mysqli_query($conn, "INSERT INTO lokasi (nama_lokasi, provinsi, koordinat) VALUES 
//         ('Taman Nasional Bromo Tengger Semeru', 'Jawa Timur', '7.9425° S, 112.9530° E'),
//         ('Taman Nasional Gunung Gede Pangrango', 'Jawa Barat', '6.7780° S, 106.9316° E'),
//         ('Taman Nasional Gunung Ciremai', 'Jawa Barat', '6.7373° S, 108.4095° E')");
    
//     mysqli_query($conn, "INSERT INTO gunung (nama_gunung, id_lokasi, ketinggian, tingkat_kesulitan, deskripsi, syarat_ketentuan) VALUES 
//         ('Semeru', 1, 3676, 'Sulit', 'Gunung tertinggi di Pulau Jawa dengan puncak Mahameru yang terkenal', 'Wajib membawa perlengkapan standar pendakian gunung tinggi, surat keterangan sehat, dan izin khusus'),
//         ('Bromo', 1, 2329, 'Sedang', 'Gunung berapi aktif dengan pemandangan sunrise yang spektakuler', 'Wajib menggunakan masker, membawa jaket tebal, dan mengikuti aturan kawasan Taman Nasional'),
//         ('Arjuno', 1, 3339, 'Sedang', 'Gunung berapi kembar dengan Welirang, memiliki jalur pendakian yang bervariasi', 'Wajib membawa perlengkapan standar, mengikuti jalur yang ditentukan, dan menjaga kebersihan'),
//         ('Welirang', 1, 3156, 'Sulit', 'Gunung berapi aktif dengan kawah sulfur yang masih ditambang tradisional', 'Wajib menggunakan masker khusus, membawa perlengkapan lengkap, dan izin khusus'),
//         ('Gede', 2, 2958, 'Sedang', 'Gunung dengan hutan hujan tropis yang masih alami dan beragam flora fauna', 'Wajib membawa perlengkapan standar, mengikuti jalur yang ditentukan, dan menjaga kebersihan'),
//         ('Pangrango', 2, 3019, 'Sulit', 'Gunung dengan vegetasi pegunungan yang masih terjaga dengan baik', 'Wajib membawa perlengkapan standar, izin khusus, dan surat keterangan sehat'),
//         ('Ciremai', 3, 3078, 'Sulit', 'Gunung tertinggi di Jawa Barat dengan pemandangan yang menakjubkan', 'Wajib membawa perlengkapan standar, surat keterangan sehat, dan mengikuti aturan Taman Nasional')");
// }

// // Membuat admin default jika belum ada
//  $admin_check = mysqli_query($conn, "SELECT * FROM users WHERE role = 'admin'");
// if (mysqli_num_rows($admin_check) == 0) {
//     $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
//     mysqli_query($conn, "INSERT INTO users (username, password, nama_lengkap, email, no_hp, role) 
//                         VALUES ('admin', '$admin_password', 'Administrator', 'admin@pendakian.com', '081234567890', 'admin')");
// }
?>