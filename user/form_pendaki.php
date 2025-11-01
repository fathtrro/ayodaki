<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user' || !isset($_SESSION['id_registrasi'])) {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

 $user_id = $_SESSION['user_id'];
 $id_registrasi = $_SESSION['id_registrasi'];
 $jumlah_orang = $_SESSION['jumlah_orang'];

// Verifikasi registrasi ada di database dan milik user yang login
 $registrasi_check = mysqli_query($conn, "SELECT * FROM registrasi WHERE id_registrasi = $id_registrasi AND id_user = $user_id");
if (mysqli_num_rows($registrasi_check) == 0) {
    // Jika registrasi tidak ditemukan, redirect ke pendaftaran
    unset($_SESSION['id_registrasi']);
    unset($_SESSION['jumlah_orang']);
    header("Location: pendaftaran.php");
    exit;
}

// Proses simpan data pendaki
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    for ($i = 0; $i < $jumlah_orang; $i++) {
        $nama = $_POST['nama'][$i];
        $email = $_POST['email'][$i];
        $alamat = $_POST['alamat'][$i];
        
        mysqli_query($conn, "INSERT INTO pendaki (id_registrasi, nama, email, alamat) 
                            VALUES ($id_registrasi, '$nama', '$email', '$alamat')");
    }
    
    // Redirect ke halaman pembayaran
    header("Location: pembayaran.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pendaki - Portal Booking Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: #198754;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">User Panel</h4>
                        <p class="text-white-50">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="pendaftaran.php">
                                <i class="bi bi-clipboard-plus me-2"></i> Pendaftaran Pendakian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="riwayat.php">
                                <i class="bi bi-clock-history me-2"></i> Riwayat Transaksi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="konfirmasi.php">
                                <i class="bi bi-check-circle me-2"></i> Konfirmasi Kedatangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="bi bi-person-circle me-2"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item mt-auto">
                            <a class="nav-link" href="../logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Data Pendaki</h1>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Input Data Pendaki</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Jumlah orang: <strong><?php echo $jumlah_orang; ?></strong> orang</p>
                        
                        <form method="post">
                            <?php for ($i = 0; $i < $jumlah_orang; $i++): ?>
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Pendaki <?php echo $i + 1; ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="nama<?php echo $i; ?>" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" id="nama<?php echo $i; ?>" name="nama[]" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="email<?php echo $i; ?>" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email<?php echo $i; ?>" name="email[]" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="alamat<?php echo $i; ?>" class="form-label">Alamat</label>
                                            <textarea class="form-control" id="alamat<?php echo $i; ?>" name="alamat[]" rows="2" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                            
                            <div class="d-flex justify-content-between">
                                <a href="pendaftaran.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" name="simpan" class="btn btn-success">
                                    Lanjut ke Pembayaran <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>