<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_pendakian";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS lokasi (
    id_lokasi INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_lokasi VARCHAR(100),
    provinsi VARCHAR(50),
    koordinat VARCHAR(50)
)");
?>