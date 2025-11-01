<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

// Statistik dashboard
 $total_gunung = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM gunung"))['total'];
 $total_pendaki = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaki"))['total'];
 $total_registrasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi"))['total'];
 $registrasi_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE status_pembayaran = 'pending'"))['total'];
 $pendaki_naik = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE status_pendakian = 'naik'"))['total'];
 $pendaki_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE status_pendakian = 'selesai'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Portal Booking Pendakian</title>
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
                        <h4 class="text-white">Admin Panel</h4>
                        <p class="text-white-50">Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>
                    </div>
                    <ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="manage_gunung.php">
            <i class="bi bi-mountain me-2"></i> Kelola Gunung
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="approve_registrasi.php">
            <i class="bi bi-check-circle me-2"></i> Approve Registrasi
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="manage_pendaki.php">
            <i class="bi bi-people me-2"></i> Kelola Pendaki
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="tracking_pendakian.php">
            <i class="bi bi-geo-alt me-2"></i> Tracking Pendakian
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="update_status_selesai.php">
            <i class="bi bi-check-circle me-2"></i> Update Status Selesai
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
                    <h1 class="h2">Dashboard Admin</h1>
                </div>

                <!-- Statistik Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Gunung</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_gunung; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-mountain stat-icon text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pendaki</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_pendaki; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people stat-icon text-gray-300"></i>
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
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Registrasi</div>
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
                </div>

                <!-- Tracking Pendakian -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-success">Pendaki Sedang Naik</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                $query = "SELECT r.id_registrasi, g.nama_gunung, u.nama_lengkap, r.tanggal_pendakian, r.tanggal_selesai
                                         FROM registrasi r
                                         JOIN users u ON r.id_user = u.id_user
                                         JOIN gunung g ON r.id_gunung = g.id_gunung
                                         WHERE r.status_pendakian = 'naik'
                                         ORDER BY r.tanggal_pendakian DESC
                                         LIMIT 5";
                                $result = mysqli_query($conn, $query);
                                
                                if (mysqli_num_rows($result) > 0) {
                                    echo '<div class="table-responsive">';
                                    echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                                    echo '<thead><tr><th>Nama</th><th>Gunung</th><th>Tanggal Naik</th><th>Tanggal Selesai</th></tr></thead>';
                                    echo '<tbody>';
                                    
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_pendakian'])) . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                        echo '</tr>';
                                    }
                                    
                                    echo '</tbody></table></div>';
                                    echo '<div class="text-center mt-2"><a href="tracking_pendakian.php" class="btn btn-sm btn-success">Lihat Semua</a></div>';
                                } else {
                                    echo '<p class="text-center">Tidak ada pendaki yang sedang naik.</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Pendaki Selesai</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                $query = "SELECT r.id_registrasi, g.nama_gunung, u.nama_lengkap, r.tanggal_pendakian, r.tanggal_selesai
                                         FROM registrasi r
                                         JOIN users u ON r.id_user = u.id_user
                                         JOIN gunung g ON r.id_gunung = g.id_gunung
                                         WHERE r.status_pendakian = 'selesai'
                                         ORDER BY r.tanggal_selesai DESC
                                         LIMIT 5";
                                $result = mysqli_query($conn, $query);
                                
                                if (mysqli_num_rows($result) > 0) {
                                    echo '<div class="table-responsive">';
                                    echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                                    echo '<thead><tr><th>Nama</th><th>Gunung</th><th>Tanggal Naik</th><th>Tanggal Selesai</th></tr></thead>';
                                    echo '<tbody>';
                                    
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                        echo '<td>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_pendakian'])) . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                        echo '</tr>';
                                    }
                                    
                                    echo '</tbody></table></div>';
                                    echo '<div class="text-center mt-2"><a href="tracking_pendakian.php" class="btn btn-sm btn-primary">Lihat Semua</a></div>';
                                } else {
                                    echo '<p class="text-center">Tidak ada pendaki yang selesai.</p>';
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