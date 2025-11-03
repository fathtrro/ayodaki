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
    <title>Approve Registrasi - MountHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, var(--primary-dark) 100%);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(34, 197, 94, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
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

        /* Badge for ID */
        .badge-id {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: var(--secondary);
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
        }

        /* Status indicators */
        .status-pending {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #d97706;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending::before {
            content: '';
            width: 6px;
            height: 6px;
            background: #d97706;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
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
                    <h1><i class="fas fa-check-circle"></i> Approve Registrasi</h1>
                    <p style="margin: 0; opacity: 0.9; font-size: 0.875rem;">Kelola dan setujui registrasi pendakian
                        yang masuk</p>
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
                        <h6><i class="fas fa-clock text-warning"></i> Daftar Registrasi Pending</h6>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nama Pendaftar</th>
                                            <th>Gunung</th>
                                            <th>Tgl Pendakian</th>
                                            <th>Tgl Selesai</th>
                                            <th>No. HP</th>
                                            <th>Jumlah Bayar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge-id">#<?php echo $row['id_registrasi']; ?></span>
                                                </td>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                        <i class="fas fa-user text-muted"></i>
                                                        <strong><?php echo htmlspecialchars($row['nama_lengkap']); ?></strong>
                                                    </div>
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
                                                    <i class="fas fa-calendar-check text-muted me-1"></i>
                                                    <?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-phone text-muted me-1"></i>
                                                    <?php echo htmlspecialchars($row['no_hp']); ?>
                                                </td>
                                                <td>
                                                    <strong style="color: var(--success);">
                                                        Rp <?php echo number_format($row['jumlah_bayar'], 0, ',', '.'); ?>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="approve_registrasi.php?approve=true&id=<?php echo $row['id_registrasi']; ?>"
                                                            class="btn btn-sm btn-success"
                                                            onclick="return confirm('Apakah Anda yakin ingin menyetujui registrasi ini? Status pendakian akan diubah menjadi \'naik\'.')">
                                                            <i class="fas fa-check"></i> Approve
                                                        </a>
                                                        <a href="approve_registrasi.php?reject=true&id=<?php echo $row['id_registrasi']; ?>"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Apakah Anda yakin ingin menolak registrasi ini? Status pendakian akan diubah menjadi \'batal\'.')">
                                                            <i class="fas fa-times"></i> Reject
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Tidak ada registrasi yang pending</p>
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
        document.addEventListener('DOMContentLoaded', function () {
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