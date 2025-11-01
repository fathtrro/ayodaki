<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

 $user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit;
}

 $id_registrasi = $_GET['id'];

// Ambil data registrasi
 $query = "SELECT r.*, g.nama_gunung, l.nama_lokasi 
          FROM registrasi r 
          JOIN gunung g ON r.id_gunung = g.id_gunung 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
          WHERE r.id_registrasi = $id_registrasi AND r.id_user = $user_id";
 $result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: riwayat.php");
    exit;
}

 $registrasi = mysqli_fetch_assoc($result);

// Ambil data pendaki
 $pendaki_result = mysqli_query($conn, "SELECT * FROM pendaki WHERE id_registrasi = $id_registrasi");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak SIMAKSI - Portal Booking Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .simaksi {
            border: 2px solid #000;
            padding: 20px;
            width: 600px;
            margin: 0 auto;
            background-color: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
        }
        .detail {
            margin-bottom: 20px;
        }
        .pendaki {
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 0.9rem;
        }
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print mb-3 text-center">
        <button onclick="window.print()" class="btn btn-success">
            <i class="bi bi-printer"></i> Cetak SIMAKSI
        </button>
        <a href="riwayat.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>
    
    <div class="simaksi">
        <div class="header">
            <img src="https://via.placeholder.com/80x80?text=LOGO" alt="Logo" class="logo">
            <h2>SIMAKSI PENDAKIAN</h2>
            <h5>Sistem Informasi Manajemen Kawasan Pendakian</h5>
        </div>
        
        <div class="detail">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>No. SIMAKSI:</strong> <?php echo $registrasi['simaksi']; ?></p>
                    <p><strong>Gunung:</strong> <?php echo htmlspecialchars($registrasi['nama_gunung']); ?></p>
                    <p><strong>Lokasi:</strong> <?php echo htmlspecialchars($registrasi['nama_lokasi']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tanggal Pendakian:</strong> <?php echo date('d/m/Y', strtotime($registrasi['tanggal_pendakian'])); ?></p>
                    <p><strong>Tanggal Selesai:</strong> <?php echo date('d/m/Y', strtotime($registrasi['tanggal_selesai'])); ?></p>
                    <p><strong>No. HP:</strong> <?php echo htmlspecialchars($registrasi['no_hp']); ?></p>
                </div>
            </div>
        </div>
        
        <h5>Data Pendaki:</h5>
        <?php while ($pendaki = mysqli_fetch_assoc($pendaki_result)): ?>
            <div class="pendaki">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($pendaki['nama']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($pendaki['email']); ?></p>
                    </div>
                </div>
                <p><strong>Alamat:</strong> <?php echo htmlspecialchars($pendaki['alamat']); ?></p>
            </div>
        <?php endwhile; ?>
        
        <div class="footer">
            <p>SIMAKSI ini berlaku untuk tanggal pendakian yang tertera di atas.</p>
            <p>Harus dibawa saat pendakian dan ditunjukkan kepada petugas.</p>
            <p class="mt-2"><strong>Dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></strong></p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>