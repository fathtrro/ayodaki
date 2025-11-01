<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

 $user_id = $_SESSION['user_id'];

// Ambil data riwayat registrasi
 $query = "SELECT r.*, g.nama_gunung, p.status_pembayaran as status_pembayaran_detail, p.tanggal_pembayaran, p.bukti_pembayaran
          FROM registrasi r
          JOIN gunung g ON r.id_gunung = g.id_gunung
          LEFT JOIN pembayaran p ON r.id_registrasi = p.id_registrasi
          WHERE r.id_user = $user_id
          ORDER BY r.tanggal_daftar DESC";
 $result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Portal Booking Pendakian</title>
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
                            <a class="nav-link active" href="riwayat.php">
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
                    <h1 class="h2">Riwayat Transaksi</h1>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Transaksi</h6>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID Registrasi</th>
                                            <th>Gunung</th>
                                            <th>Tanggal Pendakian</th>
                                            <th>Status Pembayaran</th>
                                            <th>Status Pendakian</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo $row['id_registrasi']; ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_gunung']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pendakian'])); ?></td>
                                                <td>
                                                    <?php if ($row['status_pembayaran'] == 'success'): ?>
                                                        <span class="badge bg-success status-badge">Success</span>
                                                    <?php elseif ($row['status_pembayaran'] == 'failed'): ?>
                                                        <span class="badge bg-danger status-badge">Failed</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning status-badge">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($row['status_pendakian'] == 'naik'): ?>
                                                        <span class="badge bg-success status-badge">Sedang Naik</span>
                                                    <?php elseif ($row['status_pendakian'] == 'selesai'): ?>
                                                        <span class="badge bg-primary status-badge">Selesai</span>
                                                    <?php elseif ($row['status_pendakian'] == 'batal'): ?>
                                                        <span class="badge bg-danger status-badge">Batal</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary status-badge">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_daftar'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $row['id_registrasi']; ?>">
                                                            <i class="bi bi-info-circle"></i> Detail
                                                        </button>
                                                        <?php if ($row['status_pembayaran'] == 'success' && $row['simaksi']): ?>
                                                            <a href="cetak_simaksi.php?id=<?php echo $row['id_registrasi']; ?>" class="btn btn-sm btn-success" target="_blank">
                                                                <i class="bi bi-printer"></i> SIMAKSI
                                                            </a>
                                                        <?php if ($row['status_pendakian'] == 'selesai'): ?>
                                                            <a href="konfirmasi.php?id=<?php echo $row['id_registrasi']; ?>" class="btn btn-sm btn-warning">
                                                                <i class="bi bi-check-circle"></i> Konfirmasi
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Detail -->
                                            <div class="modal fade" id="detailModal<?php echo $row['id_registrasi']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Detail Registrasi #<?php echo $row['id_registrasi']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6>Informasi Pendakian</h6>
                                                                    <table class="table table-sm table-borderless">
                                                                        <tr>
                                                                            <td width="150">Gunung</td>
                                                                            <td>: <?php echo htmlspecialchars($row['nama_gunung']); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Tanggal Pendakian</td>
                                                                            <td>: <?php echo date('d/m/Y', strtotime($row['tanggal_pendakian'])); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Tanggal Selesai</td>
                                                                            <td>: <?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>No. HP</td>
                                                                            <td>: <?php echo htmlspecialchars($row['no_hp']); ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>SIMAKSI</td>
                                                                            <td>: <?php echo $row['simaksi'] ? $row['simaksi'] : '-'; ?></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Status Pembayaran</td>
                                                                            <td>: 
                                                                                <?php if ($row['status_pembayaran'] == 'success'): ?>
                                                                                    <span class="badge bg-success">Success</span>
                                                                                <?php elseif ($row['status_pembayaran'] == 'failed'): ?>
                                                                                    <span class="badge bg-danger">Failed</span>
                                                                                <?php else: ?>
                                                                                    <span class="badge bg-warning">Pending</span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Status Pendakian</td>
                                                                            <td>: 
                                                                                <?php if ($row['status_pendakian'] == 'naik'): ?>
                                                                                    <span class="badge bg-success">Sedang Naik</span>
                                                                                <?php elseif ($row['status_pendakian'] == 'selesai'): ?>
                                                                                    <span class="badge bg-primary">Selesai</span>
                                                                                <?php elseif ($row['status_pendakian'] == 'batal'): ?>
                                                                                    <span class="badge bg-danger">Batal</span>
                                                                                <?php else: ?>
                                                                                    <span class="badge bg-secondary">Pending</span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                
                                                                <div class="col-md-6">
                                                                    <h6>Informasi Pembayaran</h6>
                                                                    <?php if ($row['tanggal_pembayaran']): ?>
                                                                        <table class="table table-sm table-borderless">
                                                                            <tr>
                                                                                <td width="150">Tanggal Pembayaran</td>
                                                                                <td>: <?php echo date('d/m/Y H:i', strtotime($row['tanggal_pembayaran'])); ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Bukti Pembayaran</td>
                                                                                <td>: 
                                                                                    <?php if ($row['bukti_pembayaran'] && file_exists($row['bukti_pembayaran'])): ?>
                                                                                        <a href="<?php echo $row['bukti_pembayaran']; ?>" target="_blank" class="btn btn-sm btn-primary">
                                                                                            <i class="bi bi-eye"></i> Lihat Bukti
                                                                                        </a>
                                                                                    <?php else: ?>
                                                                                        -
                                                                                    <?php endif; ?>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    <?php else: ?>
                                                                        <p>Belum ada data pembayaran</p>
                                                                    <?php endif; ?>
                                                                    
                                                                    <h6 class="mt-3">Data Pendaki</h6>
                                                                    <?php
                                                                    $pendaki_query = "SELECT * FROM pendaki WHERE id_registrasi = " . $row['id_registrasi'];
                                                                    $pendaki_result = mysqli_query($conn, $pendaki_query);
                                                                    
                                                                    if (mysqli_num_rows($pendaki_result) > 0):
                                                                        echo '<table class="table table-sm table-borderless">';
                                                                        while ($pendaki = mysqli_fetch_assoc($pendaki_result)):
                                                                            echo '<tr>';
                                                                            echo '<td width="150">Nama</td>';
                                                                            echo '<td>: ' . htmlspecialchars($pendaki['nama']) . '</td>';
                                                                            echo '</tr>';
                                                                            echo '<tr>';
                                                                            echo '<td>Email</td>';
                                                                            echo '<td>: ' . htmlspecialchars($pendaki['email']) . '</td>';
                                                                            echo '</tr>';
                                                                            echo '<tr>';
                                                                            echo '<td colspan="2"><hr></td>';
                                                                            echo '</tr>';
                                                                        endwhile;
                                                                        echo '</table>';
                                                                    else:
                                                                        echo '<p>Belum ada data pendaki</p>';
                                                                    endif;
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">Belum ada riwayat transaksi</p>
                                <a href="pendaftaran.php" class="btn btn-success">Daftar Pendakian Sekarang</a>
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