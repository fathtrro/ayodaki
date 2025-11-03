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
$registrasi_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pembayaran WHERE status_pembayaran = 'pending'"))['total'];
$pendaki_naik = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE status_pendakian = 'naik'"))['total'];
$pendaki_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM registrasi WHERE status_pendakian = 'selesai'"))['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MountHub</title>
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
            margin-bottom: 0.35rem;
        }

        .page-header p {
            opacity: 0.9;
            margin: 0;
            font-size: 0.875rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 1.35rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-wide {
            grid-column: span 2;
        }

        @media (max-width: 992px) {
            .stat-card-wide {
                grid-column: span 1;
            }
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: var(--accent-color);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: var(--success);
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: var(--info);
        }

        .stat-icon.info {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: var(--secondary);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: var(--warning);
        }

        .stat-icon.danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: var(--danger);
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--gray);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 0.4rem;
        }

        .stat-value {
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
        }

        .stat-description {
            font-size: 0.75rem;
            color: var(--gray);
            margin-top: 0.5rem;
            font-weight: 400;
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

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #4f46e5 100%);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.4);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2.5rem 1.5rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 0.75rem;
        }

        .empty-state p {
            color: var(--gray);
            margin: 0;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .content {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.25rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 1.5rem;
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
                    <h1><i class="fas fa-tachometer-alt"></i> Dashboard Admin</h1>
                    <p>Selamat datang kembali, <?php echo $_SESSION['nama_lengkap']; ?>! Berikut ringkasan sistem Anda.
                    </p>
                </div>

                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card" style="--accent-color: var(--success)">
                        <div class="stat-header">
                            <div class="stat-content">
                                <div class="stat-label">Total Gunung</div>
                                <div class="stat-value"><?php echo $total_gunung; ?></div>
                            </div>
                            <div class="stat-icon success">
                                <i class="fas fa-mountain"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card" style="--accent-color: var(--info)">
                        <div class="stat-header">
                            <div class="stat-content">
                                <div class="stat-label">Total Pendaki</div>
                                <div class="stat-value"><?php echo $total_pendaki; ?></div>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card" style="--accent-color: var(--secondary)">
                        <div class="stat-header">
                            <div class="stat-content">
                                <div class="stat-label">Total Registrasi</div>
                                <div class="stat-value"><?php echo $total_registrasi; ?></div>
                            </div>
                            <div class="stat-icon info">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card" style="--accent-color: var(--warning)">
                        <div class="stat-header">
                            <div class="stat-content">
                                <div class="stat-label">Pending Approval</div>
                                <div class="stat-value"><?php echo $registrasi_pending; ?></div>
                            </div>
                            <div class="stat-icon warning">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-card-wide" style="--accent-color: var(--success)">
                        <div class="stat-header">
                            <div class="stat-content">
                                <div class="stat-label">Sedang Naik</div>
                                <div class="stat-value"><?php echo $pendaki_naik; ?></div>
                                <div class="stat-description">Pendaki dalam perjalanan</div>
                            </div>
                            <div class="stat-icon success">
                                <i class="fas fa-hiking"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-card-wide" style="--accent-color: var(--info)">
                        <div class="stat-header">
                            <div class="stat-content">
                                <div class="stat-label">Selesai</div>
                                <div class="stat-value"><?php echo $pendaki_selesai; ?></div>
                                <div class="stat-description">Pendakian telah selesai</div>
                            </div>
                            <div class="stat-icon primary">
                                <i class="fas fa-flag-checkered"></i>
                            </div>
                        </div>
                    </div>

                  
                </div>

                <!-- Tracking Section -->
                <div class="row">
                    <!-- Pendaki Sedang Naik -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-hiking text-success"></i> Pendaki Sedang Naik</h6>
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
                                    echo '<table class="table">';
                                    echo '<thead><tr><th>Nama</th><th>Gunung</th><th>Tgl Naik</th></tr></thead>';
                                    echo '<tbody>';

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td><i class="fas fa-user text-muted me-2"></i>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                        echo '<td><i class="fas fa-mountain text-success me-2"></i>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                        echo '<td>' . date('d M Y', strtotime($row['tanggal_pendakian'])) . '</td>';
                                        echo '</tr>';
                                    }

                                    echo '</tbody></table></div>';
                                    echo '<div class="text-center mt-3"><a href="tracking_pendakian.php" class="btn btn-success"><i class="fas fa-arrow-right me-2"></i>Lihat Semua</a></div>';
                                } else {
                                    echo '<div class="empty-state">';
                                    echo '<i class="fas fa-hiking"></i>';
                                    echo '<p>Tidak ada pendaki yang sedang naik</p>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Pendaki Selesai -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h6><i class="fas fa-flag-checkered text-primary"></i> Pendaki Selesai</h6>
                            </div>
                            <div class="card-body">
                                <?php
                                $query = "SELECT r.id_registrasi, g.nama_gunung, u.nama_lengkap, r.tanggal_selesai
                                         FROM registrasi r
                                         JOIN users u ON r.id_user = u.id_user
                                         JOIN gunung g ON r.id_gunung = g.id_gunung
                                         WHERE r.status_pendakian = 'selesai'
                                         ORDER BY r.tanggal_selesai DESC
                                         LIMIT 5";
                                $result = mysqli_query($conn, $query);

                                if (mysqli_num_rows($result) > 0) {
                                    echo '<div class="table-responsive">';
                                    echo '<table class="table">';
                                    echo '<thead><tr><th>Nama</th><th>Gunung</th><th>Tgl Selesai</th></tr></thead>';
                                    echo '<tbody>';

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td><i class="fas fa-user text-muted me-2"></i>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                        echo '<td><i class="fas fa-mountain text-primary me-2"></i>' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                        echo '<td>' . date('d M Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                        echo '</tr>';
                                    }

                                    echo '</tbody></table></div>';
                                    echo '<div class="text-center mt-3"><a href="tracking_pendakian.php" class="btn btn-primary"><i class="fas fa-arrow-right me-2"></i>Lihat Semua</a></div>';
                                } else {
                                    echo '<div class="empty-state">';
                                    echo '<i class="fas fa-flag-checkered"></i>';
                                    echo '<p>Tidak ada pendaki yang selesai</p>';
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
    <script>
        // Animate stat cards on load
        document.addEventListener('DOMContentLoaded', function () {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(15px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.4s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 80);
            });
        });
    </script>
</body>

</html>