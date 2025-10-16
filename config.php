<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_pendakian";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Create tables using PDO
$tables = [
    "CREATE TABLE IF NOT EXISTS lokasi (
        id_lokasi INT(11) AUTO_INCREMENT PRIMARY KEY,
        nama_lokasi VARCHAR(100) NOT NULL,
        provinsi VARCHAR(50) NOT NULL,
        koordinat VARCHAR(50) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS gunung (
        id_gunung INT(11) AUTO_INCREMENT PRIMARY KEY,
        nama_gunung VARCHAR(50) NOT NULL,
        id_lokasi INT(11) NOT NULL,
        ketinggian INT(11) NOT NULL,
        tingkat_kesulitan VARCHAR(20) NOT NULL,
        deskripsi TEXT NOT NULL,
        syarat_ketentuan TEXT NOT NULL,
        FOREIGN KEY (id_lokasi) REFERENCES lokasi(id_lokasi)
    )",
    "CREATE TABLE IF NOT EXISTS registrasi (
        id_registrasi INT(11) AUTO_INCREMENT PRIMARY KEY,
        id_gunung INT(11) NOT NULL,
        tanggal_pendakian DATE NOT NULL,
        no_hp VARCHAR(20) NOT NULL,
        status_pembayaran VARCHAR(20) DEFAULT 'pending',
        simaksi VARCHAR(20) DEFAULT NULL,
        tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_gunung) REFERENCES gunung(id_gunung)
    )",
    "CREATE TABLE IF NOT EXISTS pendaki (
        id_pendaki INT(11) AUTO_INCREMENT PRIMARY KEY,
        id_registrasi INT(11) NOT NULL,
        nama VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        alamat TEXT NOT NULL,
        FOREIGN KEY (id_registrasi) REFERENCES registrasi(id_registrasi)
    )",
    "CREATE TABLE IF NOT EXISTS admins (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];

foreach ($tables as $sql) {
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        // Table might already exist, continue
    }
}

// Check if data needs to be seeded
$result = $pdo->query("SELECT COUNT(*) FROM lokasi");
if ($result->fetchColumn() == 0) {
    $pdo->exec("INSERT INTO lokasi (nama_lokasi, provinsi, koordinat) VALUES 
        ('Taman Nasional Bromo Tengger Semeru', 'Jawa Timur', '7.9425° S, 112.9530° E'),
        ('Taman Nasional Gunung Gede Pangrango', 'Jawa Barat', '6.7780° S, 106.9316° E'),
        ('Taman Nasional Gunung Ciremai', 'Jawa Barat', '6.7373° S, 108.4095° E')");

    $pdo->exec("INSERT INTO gunung (nama_gunung, id_lokasi, ketinggian, tingkat_kesulitan, deskripsi, syarat_ketentuan) VALUES 
        ('Semeru', 1, 3676, 'Sulit', 'Gunung tertinggi di Pulau Jawa dengan puncak Mahameru yang terkenal', 'Wajib membawa perlengkapan standar pendakian gunung tinggi, surat keterangan sehat, dan izin khusus'),
        ('Bromo', 1, 2329, 'Sedang', 'Gunung berapi aktif dengan pemandangan sunrise yang spektakuler', 'Wajib menggunakan masker, membawa jaket tebal, dan mengikuti aturan kawasan Taman Nasional'),
        ('Arjuno', 1, 3339, 'Sedang', 'Gunung berapi kembar dengan Welirang, memiliki jalur pendakian yang bervariasi', 'Wajib membawa perlengkapan standar, mengikuti jalur yang ditentukan, dan menjaga kebersihan'),
        ('Welirang', 1, 3156, 'Sulit', 'Gunung berapi aktif dengan kawah sulfur yang masih ditambang tradisional', 'Wajib menggunakan masker khusus, membawa perlengkapan lengkap, dan izin khusus'),
        ('Gede', 2, 2958, 'Sedang', 'Gunung dengan hutan hujan tropis yang masih alami dan beragam flora fauna', 'Wajib membawa perlengkapan standar, mengikuti jalur yang ditentukan, dan menjaga kebersihan'),
        ('Pangrango', 2, 3019, 'Sulit', 'Gunung dengan vegetasi pegunungan yang masih terjaga dengan baik', 'Wajib membawa perlengkapan standar, izin khusus, dan surat keterangan sehat'),
        ('Ciremai', 3, 3078, 'Sulit', 'Gunung tertinggi di Jawa Barat dengan pemandangan yang menakjubkan', 'Wajib membawa perlengkapan standar, surat keterangan sehat, dan mengikuti aturan Taman Nasional')");
}

// Check if admin user exists, if not create one
$adminCheck = $pdo->query("SELECT COUNT(*) FROM admins");
if ($adminCheck->fetchColumn() == 0) {
    $defaultUsername = "admin";
    $defaultPassword = password_hash("admin123", PASSWORD_BCRYPT);
    $pdo->exec("INSERT INTO admins (username, password) VALUES ('$defaultUsername', '$defaultPassword')");
}
?>