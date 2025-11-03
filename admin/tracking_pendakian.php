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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pendakian - Portal Booking Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f1f5f9;
            --white: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: var(--dark);
        }

        .content {
            padding: 2rem;
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            opacity: 0.9;
            margin: 0;
        }

        /* Alert Modern */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        /* Card Modern */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
            border-bottom: 2px solid #e2e8f0;
            padding: 1.25rem 1.5rem;
        }

        .card-header h6 {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header h6 i {
            font-size: 1.3rem;
        }

        .card-header.text-success h6 {
            color: var(--success);
        }

        .card-header.text-primary h6 {
            color: var(--secondary);
        }

        .card-header.text-danger h6 {
            color: var(--danger);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table Modern */
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
        }

        .table {
            margin: 0;
        }

        .table thead {
            background: linear-gradient(135deg, var(--dark) 0%, #1e293b 100%);
            color: white;
        }

        .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: var(--light-gray);
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e2e8f0;
        }

        /* Badge Modern */
        .badge {
            padding: 0.5rem 0.875rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bg-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
        }

        .bg-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white !important;
        }

        .bg-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        }

        /* Button Modern */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary) 0%, #4f46e5 100%);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-secondary {
            background: var(--light-gray);
            color: var(--gray);
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            color: var(--dark);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: var(--gray);
            font-size: 1rem;
            margin: 0;
        }

        /* Modal Modern */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            border: none;
        }

        .modal-title {
            font-weight: 700;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }

        /* Form Modern */
        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.success {
            background: #d1fae5;
            color: var(--success);
        }

        .stat-icon.primary {
            background: #e0e7ff;
            color: var(--secondary);
        }

        .stat-icon.danger {
            background: #fee2e2;
            color: var(--danger);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .content {
                padding: 1rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .table {
                font-size: 0.85rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../admin/navbar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content">
                <!-- Page Header -->
                <div class="page-header">
                    <h1><i class="fas fa-route"></i> Tracking Pendakian</h1>
                    <p>Pantau status dan progres pendakian secara real-time</p>
                </div>

                <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                <?php endif; ?>

                <!-- Statistics -->
                <div class="stats-grid">
                    <?php
                    $naik_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM registrasi WHERE status_pendakian = 'naik'"));
                    $selesai_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM registrasi WHERE status_pendakian = 'selesai'"));
                    $terlambat_count = mysqli_num_rows(mysqli_query($conn, "SELECT r.* FROM registrasi r LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi WHERE r.status_pendakian = 'selesai' AND r.tanggal_selesai < CURDATE() AND (k.status_kedatangan = 'belum' OR k.status_kedatangan IS NULL)"));
                    ?>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-hiking"></i>
                        </div>
                        <div class="stat-value"><?php echo $naik_count; ?></div>
                        <div class="stat-label">Sedang Naik</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $selesai_count; ?></div>
                        <div class="stat-label">Selesai</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-value"><?php echo $terlambat_count; ?></div>
                        <div class="stat-label">Terlambat</div>
                    </div>
                </div>

                <!-- Pendaki Sedang Naik -->
                <div class="card">
                    <div class="card-header text-success">
                        <h6><i class="fas fa-hiking"></i> Pendaki Sedang Naik</h6>
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
                            echo '<table class="table">';
                            echo '<thead><tr><th>ID</th><th>Gunung</th><th>Tanggal Naik</th><th>Tanggal Selesai</th><th>SIMAKSI</th><th>Aksi</th></tr></thead>';
                            echo '<tbody>';

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td><strong>#' . str_pad($row['id_registrasi'], 5, '0', STR_PAD_LEFT) . '</strong></td>';
                                echo '<td><i class="fas fa-mountain text-success"></i> ' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                echo '<td><i class="far fa-calendar"></i> ' . date('d M Y', strtotime($row['tanggal_pendakian'])) . '</td>';
                                echo '<td><i class="far fa-calendar-check"></i> ' . date('d M Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                echo '<td>' . ($row['simaksi'] ? '<span class="badge bg-success">' . $row['simaksi'] . '</span>' : '<span class="badge bg-secondary">-</span>') . '</td>';
                                echo '<td>';
                                echo '<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateStatusModal' . $row['id_registrasi'] . '">';
                                echo '<i class="fas fa-edit"></i> Update';
                                echo '</button>';
                                echo '</td>';
                                echo '</tr>';

                                // Modal Update Status
                                echo '<div class="modal fade" id="updateStatusModal' . $row['id_registrasi'] . '" tabindex="-1">';
                                echo '<div class="modal-dialog">';
                                echo '<div class="modal-content">';
                                echo '<div class="modal-header">';
                                echo '<h5 class="modal-title"><i class="fas fa-edit"></i> Update Status Pendakian</h5>';
                                echo '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>';
                                echo '</div>';
                                echo '<form method="post">';
                                echo '<div class="modal-body">';
                                echo '<input type="hidden" name="id_registrasi" value="' . $row['id_registrasi'] . '">';
                                echo '<input type="hidden" name="update_status" value="true">';
                                echo '<div class="mb-3">';
                                echo '<label class="form-label">Status Pendakian</label>';
                                echo '<select class="form-select" name="status_pendakian" required>';
                                echo '<option value="naik" selected>Sedang Naik</option>';
                                echo '<option value="selesai">Selesai</option>';
                                echo '<option value="batal">Batal</option>';
                                echo '</select>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="modal-footer">';
                                echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>';
                                echo '<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>';
                                echo '</div>';
                                echo '</form>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }

                            echo '</tbody></table></div>';
                        } else {
                            echo '<div class="empty-state">';
                            echo '<i class="fas fa-hiking"></i>';
                            echo '<p>Tidak ada pendaki yang sedang naik</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Pendaki Selesai -->
                <div class="card">
                    <div class="card-header text-primary">
                        <h6><i class="fas fa-check-circle"></i> Pendaki Selesai</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT r.id_registrasi, u.nama_lengkap, g.nama_gunung, r.tanggal_pendakian, r.tanggal_selesai, k.status_kedatangan, k.tanggal_konfirmasi
                                 FROM registrasi r
                                 JOIN users u ON r.id_user = u.id_user
                                 JOIN gunung g ON r.id_gunung = g.id_gunung
                                 LEFT JOIN konfirmasi_kedatangan k ON r.id_registrasi = k.id_registrasi
                                 WHERE r.status_pendakian = 'selesai'
                                 ORDER BY r.tanggal_selesai DESC";
                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table">';
                            echo '<thead><tr><th>ID</th><th>Nama</th><th>Gunung</th><th>Tanggal Naik</th><th>Tanggal Selesai</th><th>Status</th><th>Konfirmasi</th></tr></thead>';
                            echo '<tbody>';

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td><strong>#' . str_pad($row['id_registrasi'], 5, '0', STR_PAD_LEFT) . '</strong></td>';
                                echo '<td><i class="fas fa-user"></i> ' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                echo '<td><i class="fas fa-mountain text-primary"></i> ' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                echo '<td>' . date('d M Y', strtotime($row['tanggal_pendakian'])) . '</td>';
                                echo '<td>' . date('d M Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                echo '<td>';
                                if ($row['status_kedatangan'] == 'sudah') {
                                    echo '<span class="badge bg-success"><i class="fas fa-check"></i> Sudah</span>';
                                } else {
                                    echo '<span class="badge bg-warning"><i class="fas fa-clock"></i> Belum</span>';
                                }
                                echo '</td>';
                                echo '<td>' . ($row['tanggal_konfirmasi'] ? date('d M Y H:i', strtotime($row['tanggal_konfirmasi'])) : '<span class="text-muted">-</span>') . '</td>';
                                echo '</tr>';
                            }

                            echo '</tbody></table></div>';
                        } else {
                            echo '<div class="empty-state">';
                            echo '<i class="fas fa-check-circle"></i>';
                            echo '<p>Tidak ada pendaki yang selesai</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Pendaki Terlambat -->
                <div class="card">
                    <div class="card-header text-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Pendaki Terlambat Konfirmasi</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $query = "SELECT r.id_registrasi, u.nama_lengkap, g.nama_gunung, r.tanggal_selesai, d.jumlah_denda, d.alasan_denda, d.status_denda
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
                            echo '<table class="table">';
                            echo '<thead><tr><th>ID</th><th>Nama</th><th>Gunung</th><th>Tgl Selesai</th><th>Status</th><th>Denda</th><th>Status Denda</th><th>Aksi</th></tr></thead>';
                            echo '<tbody>';

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td><strong>#' . str_pad($row['id_registrasi'], 5, '0', STR_PAD_LEFT) . '</strong></td>';
                                echo '<td><i class="fas fa-user"></i> ' . htmlspecialchars($row['nama_lengkap']) . '</td>';
                                echo '<td><i class="fas fa-mountain text-danger"></i> ' . htmlspecialchars($row['nama_gunung']) . '</td>';
                                echo '<td>' . date('d M Y', strtotime($row['tanggal_selesai'])) . '</td>';
                                echo '<td><span class="badge bg-warning"><i class="fas fa-clock"></i> Belum</span></td>';
                                echo '<td>';
                                if ($row['jumlah_denda']) {
                                    echo '<strong class="text-danger">Rp ' . number_format($row['jumlah_denda'], 0, ',', '.') . '</strong>';
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                echo '</td>';
                                echo '<td>';
                                if ($row['status_denda'] == 'terbayar') {
                                    echo '<span class="badge bg-success"><i class="fas fa-check"></i> Terbayar</span>';
                                } elseif ($row['status_denda'] == 'pending') {
                                    echo '<span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>';
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                echo '</td>';
                                echo '<td>';
                                if (!$row['jumlah_denda']) {
                                    echo '<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tambahDendaModal' . $row['id_registrasi'] . '">';
                                    echo '<i class="fas fa-plus"></i> Denda';
                                    echo '</button>';
                                }
                                echo '</td>';
                                echo '</tr>';

                                // Modal Tambah Denda
                                echo '<div class="modal fade" id="tambahDendaModal' . $row['id_registrasi'] . '" tabindex="-1">';
                                echo '<div class="modal-dialog">';
                                echo '<div class="modal-content">';
                                echo '<div class="modal-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">';
                                echo '<h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Tambah Denda</h5>';
                                echo '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>';
                                echo '</div>';
                                echo '<form method="post">';
                                echo '<div class="modal-body">';
                                echo '<input type="hidden" name="id_registrasi" value="' . $row['id_registrasi'] . '">';
                                echo '<input type="hidden" name="tambah_denda" value="true">';
                                echo '<div class="mb-3">';
                                echo '<label class="form-label">Jumlah Denda (Rp)</label>';
                                echo '<input type="number" class="form-control" name="jumlah_denda" placeholder="Masukkan jumlah denda" required>';
                                echo '</div>';
                                echo '<div class="mb-3">';
                                echo '<label class="form-label">Alasan Denda</label>';
                                echo '<select class="form-select" name="alasan_denda" required>';
                                echo '<option value="">Pilih alasan</option>';
                                echo '<option value="Terlambat konfirmasi kedatangan">Terlambat konfirmasi kedatangan</option>';
                                echo '<option value="Melanggar aturan pendakian">Melanggar aturan pendakian</option>';
                                echo '<option value="Merusak fasilitas">Merusak fasilitas</option>';
                                echo '<option value="Lainnya">Lainnya</option>';
                                echo '</select>';
                                echo '</div>';
                                echo '</div>';
                                echo '<div class="modal-footer">';
                                echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>';
                                echo '<button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> Tambah Denda</button>';
                                echo '</div>';
                                echo '</form>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }

                            echo '</tbody></table></div>';
                        } else {
                            echo '<div class="empty-state">';
                            echo '<i class="fas fa-check-circle"></i>';
                            echo '<p>Tidak ada pendaki yang terlambat konfirmasi</p>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto dismiss alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Add animation to table rows
        const tableRows = document.querySelectorAll('.table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.opacity = '0';
            row.style.transform = 'translateY(20px)';
            setTimeout(() => {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, index * 50);
        });
    </script>
</body>
</html>