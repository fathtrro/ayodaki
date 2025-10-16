<?php
include 'config.php';

// Tambah Data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama_gunung'];
    $id_lokasi = $_POST['id_lokasi'];
    $ketinggian = $_POST['ketinggian'];
    $kesulitan = $_POST['tingkat_kesulitan'];
    $deskripsi = $_POST['deskripsi'];
    $syarat = $_POST['syarat_ketentuan'];

    mysqli_query($conn, "INSERT INTO gunung (nama_gunung, id_lokasi, ketinggian, tingkat_kesulitan, deskripsi, syarat_ketentuan)
                         VALUES ('$nama','$id_lokasi','$ketinggian','$kesulitan','$deskripsi','$syarat')");
    header("Location: index.php");
}

// Edit Data
if (isset($_POST['edit'])) {
    $id = $_POST['id_gunung'];
    $nama = $_POST['nama_gunung'];
    $id_lokasi = $_POST['id_lokasi'];
    $ketinggian = $_POST['ketinggian'];
    $kesulitan = $_POST['tingkat_kesulitan'];
    $deskripsi = $_POST['deskripsi'];
    $syarat = $_POST['syarat_ketentuan'];

    mysqli_query($conn, "UPDATE gunung SET 
        nama_gunung='$nama',
        id_lokasi='$id_lokasi',
        ketinggian='$ketinggian',
        tingkat_kesulitan='$kesulitan',
        deskripsi='$deskripsi',
        syarat_ketentuan='$syarat'
        WHERE id_gunung='$id'");
    header("Location: index.php");
}

// Hapus Data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM gunung WHERE id_gunung='$id'");
    header("Location: index.php");
}
?>