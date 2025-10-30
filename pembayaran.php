<?php
session_start();
include 'config.php';

 $id_registrasi = $_SESSION['id_registrasi'];

// Ambil data registrasi
 $result = mysqli_query($conn, "SELECT r.*, g.nama_gunung, l.nama_lokasi 
                              FROM registrasi r 
                              JOIN gunung g ON r.id_gunung = g.id_gunung 
                              JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
                              WHERE r.id_registrasi = $id_registrasi");
 $registrasi = mysqli_fetch_assoc($result);

// Ambil data pendaki
 $pendaki_result = mysqli_query($conn, "SELECT * FROM pendaki WHERE id_registrasi = $id_registrasi");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran</title>
</head>
<body>
    <h1>Konfirmasi Pembayaran</h1>
    
    <h2>Detail Pendakian</h2>
    <p><strong>Gunung:</strong> <?php echo $registrasi['nama_gunung']; ?></p>
    <p><strong>Lokasi:</strong> <?php echo $registrasi['nama_lokasi']; ?></p>
    <p><strong>Tanggal Pendakian:</strong> <?php echo $registrasi['tanggal_pendakian']; ?></p>
    <p><strong>No. HP:</strong> <?php echo $registrasi['no_hp']; ?></p>
    
    <h2>Data Pendaki</h2>
    <?php while ($pendaki = mysqli_fetch_assoc($pendaki_result)): ?>
        <p><strong>Nama:</strong> <?php echo $pendaki['nama']; ?></p>
        <p><strong>Email:</strong> <?php echo $pendaki['email']; ?></p>
        <p><strong>Alamat:</strong> <?php echo $pendaki['alamat']; ?></p>
        <hr>
    <?php endwhile; ?>
    
    <h2>Detail Pembayaran</h2>
    <p><strong>Total Pembayaran:</strong> Rp <?php echo 50000 * mysqli_num_rows($pendaki_result); ?></p>
    
    <form method="post" action="proses_pembayaran.php">
        <input type="submit" value="Bayar Sekarang">
    </form>
</body>
</html>