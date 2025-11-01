<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

// Proses update status pendakian
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $id_registrasi = $_POST['id_registrasi'];
    $status_pendakian = $_POST['status_pendakian'];
    
    if (mysqli_query($conn, "UPDATE registrasi SET status_pendakian = '$status_pendakian' WHERE id_registrasi = $id_registrasi")) {
        $success = "Status pendakian berhasil diperbarui!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Proses tambah denda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_denda'])) {
    $id_registrasi = $_POST['id_registrasi'];
    $jumlah_denda = $_POST['jumlah_denda'];
    $alasan_denda = $_POST['alasan_denda'];
    
    $query = "INSERT INTO denda (id_registrasi, jumlah_denda, alasan_denda) 
              VALUES ($id_registrasi, $jumlah_denda, '$alasan_denda')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Denda berhasil ditambahkan!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data pendaki yang sedang naik
 $query_naik = "SELECT r.id_registrasi, u.nama_lengkap, g.nama_gunung, r.tanggal_pendakian, r.tanggal_selesai, r.status_pendakian, r.simaksi
               FROM registrasi r
               JOIN users u ON r.id_user = u.id_user
               JOIN gunung g ON r.id_gunung = g.id_gunung
               WHERE r.status_pendakian = 'naik'
               ORDER BY r.tanggal_pendakian DESC";
 $result_naik = mysqli_query($conn, $query_naik);

// Ambil data pendaki yang sudah selesai
 $query_selesai = "SELECT r.id_registrasi, u.nama_lengkap, g.nama_gunung, r.tanggal_pendakian, r.tanggal_selesai, r.status_pendakian, k.status_kedatangan, k.tanggal_konfirmasi
                  FROM registrasi r
                  JOIN users u ON r.id_user = u.id_user
                  JOIN gunung g ON r.id_gunung = g.id_gunung
                  LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi
                  WHERE r.status_pendakian = 'selesai'
                  ORDER BY r.tanggal_selesai DESC";
 $result_selesai = mysqli_query($conn, $query_selesai);

// Ambil data pendaki yang terlambat konfirmasi
 $query_terlambat = "SELECT r.id_registrasi, u.nama_lengkap, g.nama_gunung, r.tanggal_selesai, k.status_kedatangan, d.jumlah_denda, d.alasan_denda, d.status_denda
                    FROM registrasi r
                    JOIN users u ON r.id_user = u.id_user
                    JOIN gunung g ON r.id_gunung = g.id_gunung
                    LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi
                    LEFT JOIN denda d ON r.id_registrasi = d.id_registrasi
                    WHERE r.status_pendakian = 'selesai' 
                    AND r.tanggal_selesai < CURDATE() 
                    AND (k.status_kedatangan = 'belum' OR k.status_kedatangan IS NULL)
                    ORDER BY r.tanggal_selesai ASC";
 $result_terlambat = mysqli_query($conn, $query_terlambat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pendakian - Portal Booking Pendakian</title>
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
                            <a class="nav-link active" href="tracking_pendakian.php">
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
                    <h1 class="h2">Tracking Pendakian</h1>
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

                <!-- Pendaki Sedang Naik -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Pendaki Sedang Naik</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT r.id_registrasi, g.nama_gunung, r.tanggal_pendakian, r.tanggal_selesai, r.simaksi
                                 FROM registrasi r
                                 JOIN gunung g ON r.id_gunung = g.id_gunung
                                 WHERE r.status_pendakian = 'naik'
                                 ORDER BY r.tanggal_pendakian DESC";
                        $result = mysqli_query($conn, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                            echo '<thead><tr><th>ID Registrasi</th><th>Gunung</th><th>Tanggal Naik</th><th>Tanggal Selesai</th><th>SIMAKSI</th><th>Aksi</th></tr></thead>';
                            echo '<tbody>';
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $row['id_registrasi'] . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($row['tanggal_pendakian'])) . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                echo '<td>' . ($row['simaksi'] ? $row['simaksi'] : '-') . '</td>';
                                echo '<td>';
                                echo '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal' . $row['id_registrasi'] . '">';
                                echo '<i class="bi bi-pencil-square"></i> Update Status';
                                echo '</button>';
                                echo '</td>';
                                echo '</tr>';

                                // Modal Update Status
                                echo '<div class="modal fade" id="updateStatusModal' . $row['id_registrasi'] . '" tabindex="-1" aria-hidden="true">';
                                echo '<div class="modal-dialog">';
                                echo '<div class="modal-content">';
                                echo '<div class="modal-header">';
                                echo '<h5 class="modal-title">Update Status Pendakian</h5>';
                                echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                echo '</div>';
                                echo '<form method="post">';
                                echo '<div class="modal-body">';
                                echo '<input type="hidden" name="id_registrasi" value="' . $row['id_registrasi'] . '">';
                                echo '<input type="hidden" name="update_status" value="true">';
                                echo '<div class="mb-3">';
                                echo '<label for="status_pendakian' . $row['id_registrasi'] . '" class="form-label">Status Pendakian</label>';
                                echo '<select class="form-select" id="status_pendakian' . $row['id_registrasi'] . '" name="status_pendakian" required>';
                                echo '<option value="naik" selected>Sedang Naik</option>';
                                echo '<option value="selesai">Selesai</option>';
                                echo '<option value="batal">Batal</option>';
                                echo '</select>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="modal-footer">';
                                echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>';
                                echo '<button type="submit" class="btn btn-primary">Update</button>';
                                echo '</div>';
                                echo '</form>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            echo '</tbody></table></div>';
                        } else {
                            echo '<div class="text-center py-4">';
                            echo '<i class="bi bi-geo-alt fs-1 text-muted"></i>';
                            echo '<p class="mt-2 text-muted">Tidak ada pendaki yang sedang naik</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Pendaki Selesai -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Pendaki Selesai</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT r.id_registrasi, u.nama_lengkap, g.nama_gunung, r.tanggal_pendakian, r.tanggal_selesai, r.status_pendakian, k.status_kedatangan, k.tanggal_konfirmasi
                                 FROM registrasi r
                                 JOIN users u ON r.id_user = u.id_user
                                 JOIN gunung g ON r.id_gunung = g.id_gunung
                                 LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi
                                 WHERE r.status_pendakian = 'selesai'
                                 ORDER BY r.tanggal_selesai DESC";
                        $result = mysqli_query($conn, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                            echo '<thead><tr><th>ID Registrasi</th><th>Nama</th><th>Gunung</th><th>Tanggal Naik</th><th>Tanggal Selesai</th><th>Status Konfirmasi</th><th>Tanggal Konfirmasi</th></tr></thead>';
                            echo '<tbody>';
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $row['id_registrasi'] . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($row['tanggal_pendakian'])) . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                echo '<td>';
                                if ($row['status_kedatangan'] == 'sudah') {
                                    echo '<span class="badge bg-success status-badge">Sudah Konfirmasi</span>';
                                } else {
                                    echo '<span class="badge bg-warning status-badge">Belum Konfirmasi</span>';
                                }
                                echo '</td>';
                                echo '<td>';
                                echo $row['tanggal_konfirmasi'] ? date('d/m/Y H:i', strtotime($row['tanggal_konfirmasi'])) : '-';
                                echo '</td>';
                                echo '</tr>';
                            }
                            
                            echo '</tbody></table></div>';
                        } else {
                            echo '<div class="text-center py-4">';
                            echo '<i class="bi bi-check-circle fs-1 text-muted"></i>';
                            echo '<p class="mt-2 text-muted">Tidak ada pendaki yang selesai</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Pendaki Terlambat Konfirmasi -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">Pendaki Terlambat Konfirmasi</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT r.id_registrasi, u.nama_lengkap, g.nama_gunung, r.tanggal_selesai, k.status_kedatangan, d.jumlah_denda, d.alasan_denda, d.status_denda
                                 FROM registrasi r
                                 JOIN users u ON r.id_user = u.id_user
                                 JOIN gunung g ON r.id_gunung = g.id_gunung
                                 LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi
                                 LEFT JOIN denda d ON r.id_registrasi = d.id_registrasi
                                 WHERE r.status_pendakian = 'selesai' 
                                 AND r.tanggal_selesai < CURDATE() 
                                 AND (k.status_kedatangan = 'belum' OR k.status_kedatangan IS NULL)
                                 ORDER BY r.tanggal_selesai ASC";
                        $result = mysqli_query($conn, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-bordered" width="100%" cellspacing="0">';
                            echo '<thead><tr><th>ID Registrasi</th><th>Nama</th><th>Gunung</th><th>Tanggal Selesai</th><th>Status Konfirmasi</th><th>Denda</th><th>Status Denda</th><th>Aksi</th></tr></thead>';
                            echo '<tbody>';
                            
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td>' . $row['id_registrasi'] . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                echo '<td>' . date('d/m/Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                echo '<td>';
                                echo '<span class="badge bg-warning status-badge">Belum Konfirmasi</span>';
                                echo '</td>';
                                echo '<td>';
                                if ($row['jumlah_denda']) {
                                    echo 'Rp ' . number_format($row['jumlah_denda'], 0, ',', '.');
                                } else {
                                    echo '-';
                                }
                                echo '</td>';
                                echo '<td>';
                                if ($row['status_denda'] == 'terbayar') {
                                    echo '<span class="badge bg-success status-badge">Terbayar</span>';
                                } elseif ($row['status_denda'] == 'pending') {
                                    echo '<span class="badge bg-warning status-badge">Pending</span>';
                                } else {
                                    echo '-';
                                }
                                echo '</td>';
                                echo '<td>';
                                if (!$row['jumlah_denda']) {
                                    echo '<button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#tambahDendaModal' . $row['id_registrasi'] . '">';
                                    echo '<i class="bi bi-exclamation-triangle"></i> Tambah Denda';
                                    echo '</button>';
                                }
                                echo '</td>';
                                echo '</tr>';

                                // Modal Tambah Denda
                                echo '<div class="modal fade" id="tambahDendaModal' . $row['id_registrasi'] . '" tabindex="-1" aria-hidden="true">';
                                echo '<div class="modal-dialog">';
                                echo '<div class="modal-content">';
                                echo '<div class="modal-header">';
                                echo '<h5 class="modal-title">Tambah Denda</h5>';
                                echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                                echo '</div>';
                                echo '<form method="post">';
                                echo '<div class="modal-body">';
                                echo '<input type="hidden" name="id_registrasi" value="' . $row['id_registrasi'] . '">';
                                echo '<input type="hidden" name="tambah_denda" value="true">';
                                echo '<div class="mb-3">';
                                echo '<label for="jumlah_denda' . $row['id_registrasi'] . '" class="form-label">Jumlah Denda (Rp)</label>';
                                echo '<input type="number" class="form-control" id="jumlah_denda' . $row['id_registrasi'] . '" name="jumlah_denda" required>';
                                echo '</div>';
                                echo '<div class="mb-3">';
                                echo '<label for="alasan_denda' . $row['id_registrasi'] . '" class="form-label">Alasan Denda</label>';
                                echo '<select class="form-select" id="alasan_denda' . $row['id_registrasi'] . '" name="alasan_denda" required>';
                                echo '<option value="Terlambat konfirmasi kedatangan">Terlambat konfirmasi kedatangan</option>';
                                echo '<option value="Melanggar aturan pendakian">Melanggar aturan pendakian</option>';
                                echo '<option value="Merusak fasilitas">Merusak fasilitas</option>';
                                echo '<option value="Lainnya">Lainnya</option>';
                                echo '</select>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="modal-footer">';
                                echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>';
                                echo '<button type="submit" class="btn btn-danger">Tambah Denda</button>';
                                echo '</div>';
                                echo '</form>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            
                            echo '</tbody></table></div>';
                        } else {
                            echo '<div class="text-center py-4">';
                            echo '<i class="bi bi-check-circle fs-1 text-muted"></i>';
                            echo '<p class="mt-2 text-muted">Tidak ada pendaki yang terlambat konfirmasi</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>