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
    <title>Kelola Pendaki - MountHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --secondary: #6366f1;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #22c55e;
            --info: #3b82f6;
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f1f5f9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: var(--dark);
            font-size: 14px;
        }

        .content {
            padding: 1.5rem;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: 0 3px 5px -1px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        /* Alert */
        .alert {
            border-radius: 12px;
            border: none;
            font-size: 0.85rem;
            padding: 1rem 1.25rem;
        }

        /* Card Modern */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.25rem;
        }

        .card-header {
            background: linear-gradient(135deg, white 0%, var(--light-gray) 100%);
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem 1.25rem;
            border-radius: 14px 14px 0 0;
        }

        .card-header h6 {
            font-size: 0.95rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Table Modern */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table {
            margin: 0;
            font-size: 0.85rem;
        }

        .table thead {
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            color: white;
        }

        .table thead th {
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: var(--light-gray);
        }

        .table tbody td {
            padding: 0.75rem;
            vertical-align: middle;
            border-color: #e2e8f0;
        }

        /* Button Modern */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #4f46e5 100%);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        /* Badge for ID */
        .badge-id {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: var(--secondary);
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }

        .empty-state i {
            font-size: 3.5rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--gray);
            margin: 0;
            font-size: 0.9rem;
        }

        /* Modal */
        .modal-content {
            border-radius: 14px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 14px 14px 0 0;
            padding: 1.25rem 1.5rem;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 0.85rem;
            padding: 0.6rem 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        @media (max-width: 768px) {
            .content {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.25rem;
            }

            .table {
                font-size: 0.75rem;
            }

            .btn-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'navbar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1><i class="fas fa-users"></i> Kelola Pendaki</h1>
                    <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Kelola data pendaki yang terdaftar dalam sistem</p>
                </div>

                <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h6><i class="fas fa-list text-primary"></i> Daftar Pendaki</h6>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Alamat</th>
                                                <th>Pendaftar</th>
                                                <th>Gunung</th>
                                                <th>Tgl Pendakian</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge-id">#<?php echo $row['id_pendaki']; ?></span>
                                                        </td>
                                                        <td>
                                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                                <i class="fas fa-user-circle text-primary"></i>
                                                                <strong><?php echo htmlspecialchars($row['nama']); ?></strong>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <i class="fas fa-envelope text-muted me-1"></i>
                                                            <?php echo htmlspecialchars($row['email']); ?>
                                                        </td>
                                                        <td>
                                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                            <?php echo htmlspecialchars(substr($row['alamat'], 0, 30)) . '...'; ?>
                                                        </td>
                                                        <td>
                                                            <i class="fas fa-user text-muted me-1"></i>
                                                            <?php echo htmlspecialchars($row['nama_pendaftar']); ?>
                                                        </td>
                                                        <td>
                                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                                <i class="fas fa-mountain text-success"></i>
                                                                <?php echo htmlspecialchars($row['nama_gunung']); ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <i class="fas fa-calendar-day text-muted me-1"></i>
                                                            <?php echo date('d/m/Y', strtotime($row['tanggal_pendakian'])); ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPendakiModal<?php echo $row['id_pendaki']; ?>">
                                                                    <i class="fas fa-pencil-alt"></i> Edit
                                                                </button>
                                                                <a href="manage_pendaki.php?hapus=true&id=<?php echo $row['id_pendaki']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data pendaki ini?')">
                                                                    <i class="fas fa-trash"></i>
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
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p>Tidak ada data pendaki</p>
                                </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animate table rows on load
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.4s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>