<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

// Proses edit pendaki
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_pendaki'])) {
    $id_pendaki = $_POST['id_pendaki'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    
    $query = "UPDATE pendaki SET 
              nama = '$nama', 
              email = '$email', 
              alamat = '$alamat' 
              WHERE id_pendaki = $id_pendaki";
    
    if (mysqli_query($conn, $query)) {
        $success = "Data pendaki berhasil diperbarui!";
    } else {
        $error = "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}

// Proses hapus pendaki
if (isset($_GET['hapus']) && $_GET['hapus'] == 'true' && isset($_GET['id'])) {
    $id_pendaki = $_GET['id'];
    
    if (mysqli_query($conn, "DELETE FROM pendaki WHERE id_pendaki = $id_pendaki")) {
        $success = "Data pendaki berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data pendaki
 $query = "SELECT p.*, r.id_registrasi, u.nama_lengkap as nama_pendaftar, g.nama_gunung, r.tanggal_pendakian
          FROM pendaki p
          JOIN registrasi r ON p.id_registrasi = r.id_registrasi
          JOIN users u ON r.id_user = u.id_user
          JOIN gunung g ON r.id_gunung = g.id_gunung
          ORDER BY p.nama";
 $result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pendaki - Portal Booking Pendakian</title>
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
                            <a class="nav-link" href="approve_registrasi.php">
                                <i class="bi bi-check-circle me-2"></i> Approve Registrasi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="manage_pendaki.php">
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
                    <h1 class="h2">Kelola Pendaki</h1>
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
                        <h6 class="m-0 font-weight-bold text-primary">Daftar Pendaki</h6>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Alamat</th>
                                            <th>Pendaftar</th>
                                            <th>Gunung</th>
                                            <th>Tanggal Pendakian</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo $row['id_pendaki']; ?></td>
                                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($row['alamat'], 0, 50)) . '...'; ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_pendaftar']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_gunung']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($row['tanggal_pendakian'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPendakiModal<?php echo $row['id_pendaki']; ?>">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </button>
                                                        <a href="manage_pendaki.php?hapus=true&id=<?php echo $row['id_pendaki']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data pendaki ini?')">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Edit Pendaki -->
                                            <div class="modal fade" id="editPendakiModal<?php echo $row['id_pendaki']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Data Pendaki</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id_pendaki" value="<?php echo $row['id_pendaki']; ?>">
                                                                <input type="hidden" name="edit_pendaki" value="true">
                                                                
                                                                <div class="mb-3">
                                                                    <label for="nama<?php echo $row['id_pendaki']; ?>" class="form-label">Nama Lengkap</label>
                                                                    <input type="text" class="form-control" id="nama<?php echo $row['id_pendaki']; ?>" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="email<?php echo $row['id_pendaki']; ?>" class="form-label">Email</label>
                                                                    <input type="email" class="form-control" id="email<?php echo $row['id_pendaki']; ?>" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="alamat<?php echo $row['id_pendaki']; ?>" class="form-label">Alamat</label>
                                                                    <textarea class="form-control" id="alamat<?php echo $row['id_pendaki']; ?>" name="alamat" rows="3" required><?php echo htmlspecialchars($row['alamat']); ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-people fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">Tidak ada data pendaki</p>
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