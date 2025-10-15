<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_pendakian";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Buat tabel gunung jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS gunung (
    id_gunung INT AUTO_INCREMENT PRIMARY KEY,
    nama_gunung VARCHAR(100) NOT NULL,
    id_lokasi VARCHAR(100) NOT NULL,
    ketinggian INT(11) NOT NULL,
    deskripsi TEXT
)");

?>