// user/konfirmasi.php (diperbarui)
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

 $user_id = $_SESSION['user_id'];

// Proses konfirmasi kedatangan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['konfirmasi'])) {
    $id_registrasi = $_POST['id_registrasi'];
    $keterangan = $_POST['keterangan'];
    
    // Cek apakah sudah ada data konfirmasi
    $check_query = "SELECT * FROM konfirmasi_kedatangan WHERE id_registrasi = $id_registrasi";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update data konfirmasi
        $query = "UPDATE konfirmasi_kedatangan 
                  SET status_kedatangan = 'sudah', 
                      tanggal_konfirmasi = NOW(), 
                      keterangan = '$keterangan' 
                  WHERE id_registrasi = $id_registrasi";
    } else {
        // Insert data konfirmasi baru
        $query = "INSERT INTO konfirmasi_kedatangan (id_registrasi, status_kedatangan, tanggal_konfirmasi, keterangan) 
                  VALUES ($id_registrasi, 'sudah', NOW(), '$keterangan')";
    }
    
    if (mysqli_query($conn, $query)) {
        $success = "Konfirmasi kedatangan berhasil!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data registrasi yang perlu dikonfirmasi
 $query = "SELECT r.id_registrasi, g.nama_gunung, r.tanggal_selesai, k.status_kedatangan, k.tanggal_konfirmasi
          FROM registrasi r
          JOIN gunung g ON r.id_gunung = g.id_gunung
          LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi
          WHERE r.id_user = $user_id 
          AND r.status_pendakian = 'selesai'
          ORDER BY r.tanggal_selesai DESC";
 $result = mysqli_query($conn, $query);

// Ambil data registrasi spesifik jika ada parameter id
 $selected_registrasi = null;
if (isset($_GET['id'])) {
    $id_registrasi = $_GET['id'];
    $selected_query = "SELECT r.*, g.nama_gunung 
                      FROM registrasi r
                      JOIN gunung g ON r.id_gunung = g.id_gunung
                      WHERE r.id_registrasi = $id_registrasi AND r.id_user = $user_id";
    $selected_result = mysqli_query($conn, $selected_query);
    
    if (mysqli_num_rows($selected_result) > 0) {
        $selected_registrasi = mysqli_fetch_assoc($selected_result);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Kedatangan - Portal Booking Pendakian</title>
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
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
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
                            <a class="nav-link active" href="konfirmasi.php">
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
                    <h1 class="h2">Konfirmasi Kedatangan</h1>
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

                <?php if ($selected_registrasi): ?>
                    <!-- Form Konfirmasi -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Konfirmasi Kedatangan</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Konfirmasikan kedatangan Anda setelah selesai melakukan pendakian. Konfirmasi yang terlambat dapat dikenakan denda.
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>Informasi Pendakian</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150">ID Registrasi</td>
                                            <td>: <?php echo $selected_registrasi['id_registrasi']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Gunung</td>
                                            <td>: <?php echo htmlspecialchars($selected_registrasi['nama_gunung']); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Pendakian</td>
                                            <td>: <?php echo date('d/m/Y', strtotime($selected_registrasi['tanggal_pendakian'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Selesai</td>
                                            <td>: <?php echo date('d/m/Y', strtotime($selected_registrasi['tanggal_selesai'])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5>Peringatan</h5>
                                    <?php 
                                    $tanggal_selesai = new DateTime($selected_registrasi['tanggal_selesai']);
                                    $today = new DateTime();
                                    $selisih = $today->diff($tanggal_selesai);
                                    
                                    if ($selisih->days > 0 && $tanggal_selesai < $today) {
                                        echo '<div class="alert alert-danger" role="alert">';
                                        echo '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
                                        echo 'Anda terlambat ' . $selisih->days . ' hari untuk konfirmasi! Segera konfirmasi untuk menghindari denda.';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="alert alert-success" role="alert">';
                                        echo '<i class="bi bi-check-circle-fill me-2"></i>';
                                        echo 'Silahkan konfirmasi kedatangan Anda.';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <form method="post">
                                <input type="hidden" name="id_registrasi" value="<?php echo $selected_registrasi['id_registrasi']; ?>">
                                
                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Berikan keterangan tentang pendakian Anda..."></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="konfirmasi.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                    <button type="submit" name="konfirmasi" class="btn btn-warning">
                                        <i class="bi bi-check-circle"></i> Konfirmasi Kedatangan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Daftar Konfirmasi -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Konfirmasi Kedatangan</h6>
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>ID Registrasi</th>
                                                <th>Gunung</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Status Konfirmasi</th>
                                                <th>Tanggal Konfirmasi</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                <tr>
                                                    <td><?php echo $row['id_registrasi']; ?></td>
                                                    <td><?php echo htmlspecialchars($row['nama_gunung']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?></td>
                                                    <td>
                                                        <?php if ($row['status_kedatangan'] == 'sudah'): ?>
                                                            <span class="badge bg-success status-badge">Sudah Konfirmasi</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning status-badge">Belum Konfirmasi</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['tanggal_konfirmasi'] ? date('d/m/Y H:i', strtotime($row['tanggal_konfirmasi'])) : '-'; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($row['status_kedatangan'] != 'sudah'): ?>
                                                            <a href="konfirmasi.php?id=<?php echo $row['id_registrasi']; ?>" class="btn btn-sm btn-warning">
                                                                <i class="bi bi-check-circle"></i> Konfirmasi
                                                            </a>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-success" disabled>
                                                                <i class="bi bi-check-circle"></i> Terkonfirmasi
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-check-circle fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">Tidak ada pendakian yang perlu dikonfirmasi</p>
                                    <p class="text-muted">Pastikan status pendakian Anda sudah diubah menjadi 'selesai' oleh admin.</p>
                                    <a href="pendaftaran.php" class="btn btn-success">Daftar Pendakian Sekarang</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>