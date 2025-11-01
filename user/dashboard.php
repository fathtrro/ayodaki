<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

 $user_id = $_SESSION['user_id'];

// Statistik user
 $total_registrasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE id_user = $user_id"))['total'];
 $registrasi_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE id_user = $user_id AND status_pembayaran = 'pending'"))['total'];
 $registrasi_success = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE id_user = $user_id AND status_pembayaran = 'success'"))['total'];
 $pendakian_naik = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE id_user = $user_id AND status_pendakian = 'naik'"))['total'];
 $pendakian_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE id_user = $user_id AND status_pendakian = 'selesai'"))['total'];
 $konfirmasi_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi r 
                                                           LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi 
                                                           WHERE r.id_user = $user_id 
                                                           AND r.status_pendakian = 'selesai' 
                                                           AND (k.status_kedatangan = 'belum' OR k.status_kedatangan IS NULL)"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Portal Booking Pendakian</title>
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
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
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
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pendaftaran.php">
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
                    <h1 class="h2">Dashboard User</h1>
                </div>

                <!-- Statistik Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Registrasi</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_registrasi; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-clipboard-check stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Registrasi Pending</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $registrasi_pending; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-hourglass-split stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Registrasi Success</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $registrasi_success; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-check-circle stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Konfirmasi Pending</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $konfirmasi_pending; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-exclamation-circle stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pendakian Aktif -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-success">Pendakian Aktif</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                $query = "SELECT r.id_registrasi, g.nama_gunung, r.tanggal_pendakian, r.tanggal_selesai, r.simaksi
                                         FROM registrasi r
                                         JOIN gunung g ON r.id_gunung = g.id_gunung
                                         WHERE r.id_user = $user_id AND r.status_pendakian = 'naik'
                                         ORDER BY r.tanggal_pendakian DESC";
                                $result = mysqli_query($conn, $query);
                                
                                if (mysqli_num_rows($result) > 0) {
                                    echo '<div class="table-responsive">';
                                    echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                                    echo '<thead><tr><th>Gunung</th><th>Tanggal Naik</th><th>Tanggal Selesai</th><th>SIMAKSI</th></tr></thead>';
                                    echo '<tbody>';
                                    
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_pendakian'])) . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                        echo '<td>' . ($row['simaksi'] ? $row['simaksi'] : '-') . '</td>';
                                        echo '</tr>';
                                    }
                                    
                                    echo '</tbody></table></div>';
                                } else {
                                    echo '<div class="text-center py-4">';
                                    echo '<i class="bi bi-geo-alt fs-1 text-muted"></i>';
                                    echo '<p class="mt-2 text-muted">Tidak ada pendakian aktif</p>';
                                    echo '<a href="pendaftaran.php" class="btn btn-success btn-sm">Daftar Pendakian</a>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Konfirmasi Kedatangan Pending -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-warning">Konfirmasi Kedatangan Pending</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                $query = "SELECT r.id_registrasi, g.nama_gunung, r.tanggal_selesai
                                         FROM registrasi r
                                         JOIN gunung g ON r.id_gunung = g.id_gunung
                                         LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi
                                         WHERE r.id_user = $user_id 
                                         AND r.status_pendakian = 'selesai' 
                                         AND (k.status_kedatangan = 'belum' OR k.status_kedatangan IS NULL)
                                         ORDER BY r.tanggal_selesai ASC";
                                $result = mysqli_query($conn, $query);
                                
                                if (mysqli_num_rows($result) > 0) {
                                    echo '<div class="table-responsive">';
                                    echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                                    echo '<thead><tr><th>Gunung</th><th>Tanggal Selesai</th><th>Aksi</th></tr></thead>';
                                    echo '<tbody>';
                                    
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                        echo '<td><a href="konfirmasi.php?id=' . $row['id_registrasi'] . '" class="btn btn-warning btn-sm">Konfirmasi</a></td>';
                                        echo '</tr>';
                                    }
                                    
                                    echo '</tbody></table></div>';
                                    echo '<div class="alert alert-warning mt-2" role="alert">';
                                    echo '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
                                    echo 'Segera konfirmasi kedatangan Anda untuk menghindari denda!';
                                    echo '</div>';
                                } else {
                                    echo '<div class="text-center py-4">';
                                    echo '<i class="bi bi-check-circle fs-1 text-muted"></i>';
                                    echo '<p class="mt-2 text-muted">Tidak ada konfirmasi pending</p>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>