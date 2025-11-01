<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

// Proses approve registrasi
if (isset($_GET['approve']) && isset($_GET['id'])) {
    $id_registrasi = $_GET['id'];
    
    // Generate SIMAKSI
    $simaksi = "SIM" . rand(10000, 99999);
    
    // Update status pembayaran, SIMAKSI, dan status pendakian
    if (mysqli_query($conn, "UPDATE registrasi SET status_pembayaran = 'success', simaksi = '$simaksi', status_pendakian = 'naik' WHERE id_registrasi = $id_registrasi")) {
        $success = "Registrasi berhasil diapprove! Status pendakian diubah menjadi 'naik'.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Proses reject registrasi
if (isset($_GET['reject']) && isset($_GET['id'])) {
    $id_registrasi = $_GET['id'];
    
    if (mysqli_query($conn, "UPDATE registrasi SET status_pembayaran = 'failed', status_pendakian = 'batal' WHERE id_registrasi = $id_registrasi")) {
        $success = "Registrasi berhasil ditolak! Status pendakian diubah menjadi 'batal'.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data registrasi pending
 $query = "SELECT r.id_registrasi, u.nama_lengkap, g.nama_gunung, r.tanggal_pendakian, r.tanggal_selesai, r.no_hp, p.jumlah_bayar
          FROM registrasi r
          JOIN users u ON r.id_user = u.id_user
          JOIN gunung g ON r.id_gunung = g.id_gunung
          LEFT JOIN pembayaran p ON r.id_registrasi = p.id_registrasi
          WHERE r.status_pembayaran = 'pending'
          ORDER BY r.tanggal_daftar DESC";
 $result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Registrasi - Portal Booking Pendakian</title>
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
                            <a class="nav-link active" href="approve_registrasi.php">
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
                    <h1 class="h2">Approve Registrasi</h1>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Daftar Registrasi Pending</h6>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID Registrasi</th>
                                            <th>Nama Pendaftar</th>
                                            <th>Gunung</th>
                                            <th>Tanggal Pendakian</th>
                                            <th>Tanggal Selesai</th>
                                            <th>No. HP</th>
                                            <th>Jumlah Bayar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo $row['id_registrasi']; ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_gunung']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pendakian'])); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?></td>
                                                <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                                                <td>Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="approve_registrasi.php?approve=true&id=<?php echo $row['id_registrasi']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Apakah Anda yakin ingin menyetujui registrasi ini? Status pendakian akan diubah menjadi \'naik\'.')">
                                                            <i class="bi bi-check-circle"></i> Approve
                                                        </a>
                                                        <a href="approve_registrasi.php?reject=true&id=<?php echo $row['id_registrasi']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak registrasi ini? Status pendakian akan diubah menjadi \'batal\'.')">
                                                            <i class="bi bi-x-circle"></i> Reject
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">Tidak ada registrasi yang pending</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>