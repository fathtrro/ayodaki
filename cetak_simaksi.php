<?php
session_start();
include 'config.php';

 $simaksi = $_SESSION['simaksi'];
 $id_registrasi = $_SESSION['id_registrasi'];

// Ambil data registrasi
 $result = mysqli_query($conn, "SELECT r.*, g.nama_gunung 
                              FROM registrasi r 
                              JOIN gunung g ON r.id_gunung = g.id_gunung 
                              WHERE r.id_registrasi = $id_registrasi");
 $registrasi = mysqli_fetch_assoc($result);

// Ambil data pendaki
 $pendaki_result = mysqli_query($conn, "SELECT * FROM pendaki WHERE id_registrasi = $id_registrasi");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak SIMAKSI</title>
    <style>
        .simaksi {
            border: 2px solid #000;
            padding: 20px;
            width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .detail {
            margin-bottom: 20px;
        }
        .pendaki {
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="simaksi">
        <div class="header">
            <h1>SIMAKSI PENDAKIAN</h1>
            <h2>Sistem Informasi Manajemen Kawasan Pendakian</h2>
        </div>
        
        <div class="detail">
            <p><strong>No. SIMAKSI:</strong> <?php echo $simaksi; ?></p>
            <p><strong>Gunung:</strong> <?php echo $registrasi['nama_gunung']; ?></p>
            <p><strong>Tanggal Pendakian:</strong> <?php echo $registrasi['tanggal_pendakian']; ?></p>
            <p><strong>No. HP:</strong> <?php echo $registrasi['no_hp']; ?></p>
        </div>
        
        <h3>Data Pendaki:</h3>
        <?php while ($pendaki = mysqli_fetch_assoc($pendaki_result)): ?>
            <div class="pendaki">
                <p><strong>Nama Lengkap:</strong> <?php echo $pendaki['nama']; ?></p>
                <p><strong>Alamat:</strong> <?php echo $pendaki['alamat']; ?></p>
            </div>
        <?php endwhile; ?>
        
        <div style="text-align: center;">
            <button onclick="window.print()">Cetak SIMAKSI</button>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php">Kembali ke Halaman Utama</a>
    </div>
</body>
</html>