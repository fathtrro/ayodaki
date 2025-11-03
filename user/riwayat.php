<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

include '../config.php';

$user_id = $_SESSION['user_id'];

// Handle delete request
if (isset($_POST['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_POST['delete_id']);

    // Cek apakah registrasi milik user ini
    $check_query = "SELECT id_registrasi FROM registrasi WHERE id_registrasi = '$delete_id' AND id_user = '$user_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Hapus data terkait secara berurutan
        mysqli_query($conn, "DELETE FROM pendaki WHERE id_registrasi = '$delete_id'");
        mysqli_query($conn, "DELETE FROM pembayaran WHERE id_registrasi = '$delete_id'");
        mysqli_query($conn, "DELETE FROM registrasi WHERE id_registrasi = '$delete_id'");

        $_SESSION['success_message'] = "Riwayat transaksi berhasil dihapus!";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus riwayat transaksi!";
    }

    header("Location: riwayat.php");
    exit;
}

// Ambil data riwayat registrasi user
$query = "SELECT r.*, g.nama_gunung, g.ketinggian, l.nama_lokasi, l.provinsi,
          p.jumlah_bayar, p.metode_pembayaran, p.bukti_pembayaran, p.status_pembayaran,
          (SELECT COUNT(*) FROM pendaki WHERE id_registrasi = r.id_registrasi) as jumlah_pendaki
          FROM registrasi r 
          JOIN gunung g ON r.id_gunung = g.id_gunung 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi
          LEFT JOIN pembayaran p ON r.id_registrasi = p.id_registrasi
          WHERE r.id_user = $user_id
          ORDER BY r.id_registrasi DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - MountHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f1f5f9;
            --white: #ffffff;
            --danger: #ef4444;
            --danger-dark: #dc2626;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #fafafa;
            color: var(--dark);
            line-height: 1.6;
        }

       
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 3rem 0;
            margin-bottom: 3rem;
            color: white;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .container-main {
            padding: 0 0 4rem;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .history-card {
            background: var(--white);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 2px solid transparent;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .history-card:hover {
            border-color: var(--primary);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-gray);
        }

        .mountain-info {
            flex: 1;
        }

        .mountain-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mountain-location {
            color: var(--gray);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-belum-bayar {
            background: #e0e7ff;
            color: #3730a3;
        }

        .history-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            background: var(--light-gray);
            padding: 1rem;
            border-radius: 10px;
        }

        .detail-label {
            font-size: 0.8rem;
            color: var(--gray);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .history-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-modern {
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
            color: white;
        }

        .btn-outline-modern {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline-modern:hover {
            background: var(--primary);
            color: white;
        }

        .btn-secondary-modern {
            background: var(--light-gray);
            color: var(--gray);
        }

        .btn-secondary-modern:hover {
            background: #e2e8f0;
            color: var(--dark);
        }

        .btn-danger-modern {
            background: var(--danger);
            color: white;
        }

        .btn-danger-modern:hover {
            background: var(--danger-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .empty-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 3.5rem;
        }

        .empty-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }

        .empty-text {
            color: var(--gray);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .filter-title {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.625rem 1rem;
            font-weight: 500;
        }

        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        footer {
            background: var(--dark);
            color: white;
            padding: 2rem 0;
            margin-top: 5rem;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 16px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
        }

        .modal-header.modal-danger {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
        }

        .modal-title {
            font-weight: 700;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 2rem;
        }

        .info-table {
            width: 100%;
        }

        .info-table tr {
            border-bottom: 1px solid var(--light-gray);
        }

        .info-table td {
            padding: 0.75rem 0;
        }

        .info-table td:first-child {
            font-weight: 600;
            color: var(--gray);
            width: 180px;
        }

        .bukti-image {
            max-width: 100%;
            border-radius: 12px;
            border: 2px solid var(--light-gray);
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.75rem;
            }

            .history-header {
                flex-direction: column;
                gap: 1rem;
            }

            .history-details {
                grid-template-columns: 1fr;
            }

            .history-actions {
                flex-direction: column;
            }

            .btn-modern {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <style>
    /* Navbar Modern */
        .navbar {
            background: var(--white) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1.2rem 0;
            backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        .nav-link {
            color: var(--gray) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            background: var(--light-gray);
        }

        .nav-link i {
            margin-right: 0.4rem;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--light-gray);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            color: var(--dark);
        }

        .profile-btn:hover {
            background: #e2e8f0;
            color: var(--primary);
        }

        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            min-width: 220px;
            padding: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .dropdown-menu-custom.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header-custom {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
        }

        .dropdown-user-name {
            font-weight: 700;
            color: var(--dark);
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .dropdown-user-role {
            font-size: 0.8rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dropdown-item-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .dropdown-item-custom:hover {
            background: var(--light-gray);
            color: var(--primary);
        }

        .dropdown-item-custom i {
            width: 20px;
            text-align: center;
        }

        .dropdown-divider-custom {
            height: 1px;
            background: #e2e8f0;
            margin: 0.5rem 0;
        }

        .dropdown-item-custom.logout {
            color: #ef4444;
        }

        .dropdown-item-custom.logout:hover {
            background: #fee2e2;
            color: #dc2626;
        }
           .navbar {
            background: var(--white) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1.2rem 0;
            backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark) !important;
            letter-spacing: -0.5px;
        }

        .navbar-brand i {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        .nav-link {
            color: var(--gray) !important;
            font-weight: 500;
            font-size: 0.95rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            background: var(--light-gray);
        }

        .nav-link i {
            margin-right: 0.4rem;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--light-gray);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            color: var(--dark);
        }

        .profile-btn:hover {
            background: #e2e8f0;
            color: var(--primary);
        }

        .profile-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .dropdown-menu-custom {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            min-width: 220px;
            padding: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .dropdown-menu-custom.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-header-custom {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 0.5rem;
        }

        .dropdown-user-name {
            font-weight: 700;
            color: var(--dark);
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .dropdown-user-role {
            font-size: 0.8rem;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .dropdown-item-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .dropdown-item-custom:hover {
            background: var(--light-gray);
            color: var(--primary);
        }

        .dropdown-item-custom i {
            width: 20px;
            text-align: center;
        }

        .dropdown-divider-custom {
            height: 1px;
            background: #e2e8f0;
            margin: 0.5rem 0;
        }

        .dropdown-item-custom.logout {
            color: #ef4444;
        }

        .dropdown-item-custom.logout:hover {
            background: #fee2e2;
            color: #dc2626;
        }
</style>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <i class="fas fa-mountain"></i>AyoDaki
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin/dashboard.php">
                                    <i class="fas fa-th-large"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">
                                    <i class="fas fa-sign-out-alt"></i>Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- Profile Dropdown for User -->
                            <li class="nav-item profile-dropdown">
                                <button class="profile-btn" id="profileBtn">
                                    <div class="profile-avatar">
                                        <?php
                                        // Get first letter of username
                                        $username = $_SESSION['username'] ?? 'U';
                                        echo strtoupper(substr($username, 0, 1));
                                        ?>
                                    </div>
                                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                                </button>
                                <div class="dropdown-menu-custom" id="profileDropdown">
                                    <div class="dropdown-header-custom">
                                        <div class="dropdown-user-name"><?php echo htmlspecialchars($_SESSION['username']); ?>
                                        </div>
                                        <div class="dropdown-user-role">User Account</div>
                                    </div>
    

                                    <div class="dropdown-divider-custom"></div>
                                    <a href="logout.php" class="dropdown-item-custom logout">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </a>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
   
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">
                <i class="fas fa-history"></i> Riwayat Transaksi
            </h1>
            <p class="page-subtitle">Lihat semua riwayat pendaftaran dan pembayaran pendakian Anda</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-main">
        <div class="container">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-title">
                        <i class="fas fa-filter"></i>
                        Filter Status
                    </div>
                    <select class="form-select" id="statusFilter" onchange="filterStatus()">
                        <option value="all">Semua Status</option>
                        <option value="pending">Menunggu Verifikasi</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <!-- History List -->
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    // Tentukan status
                    if (empty($row['bukti_pembayaran'])) {
                        $status = 'belum-bayar';
                        $status_text = 'Belum Bayar';
                        $status_icon = 'fas fa-clock';
                    } else {
                        $status = $row['status_pembayaran'] ?? 'pending';
                        if ($status == 'approved') {
                            $status_text = 'Disetujui';
                            $status_icon = 'fas fa-check-circle';
                        } elseif ($status == 'rejected') {
                            $status_text = 'Ditolak';
                            $status_icon = 'fas fa-times-circle';
                        } else {
                            $status_text = 'Menunggu Verifikasi';
                            $status_icon = 'fas fa-hourglass-half';
                        }
                    }
                    ?>
                    <div class="history-card" data-status="<?php echo $status; ?>">
                        <div class="history-header">
                            <div class="mountain-info">
                                <div class="mountain-name">
                                    <i class="fas fa-mountain" style="color: var(--primary);"></i>
                                    <?php echo htmlspecialchars($row['nama_gunung']); ?>
                                </div>
                                <div class="mountain-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($row['nama_lokasi'] . ', ' . $row['provinsi']); ?>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo $status; ?>">
                                <i class="<?php echo $status_icon; ?>"></i>
                                <?php echo $status_text; ?>
                            </span>
                        </div>

                        <div class="history-details">
                            <div class="detail-item">
                                <div class="detail-label">Tanggal Pendakian</div>
                                <div class="detail-value">
                                    <i class="fas fa-calendar" style="color: var(--primary); font-size: 0.9rem;"></i>
                                    <?php echo date('d M Y', strtotime($row['tanggal_pendakian'])); ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Jumlah Pendaki</div>
                                <div class="detail-value">
                                    <i class="fas fa-users" style="color: var(--primary); font-size: 0.9rem;"></i>
                                    <?php echo $row['jumlah_pendaki']; ?> Orang
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Total Pembayaran</div>
                                <div class="detail-value" style="color: var(--primary);">
                                    <?php echo !empty($row['jumlah_bayar']) ? 'Rp ' . number_format($row['jumlah_bayar'], 0, ',', '.') : '-'; ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">ID Registrasi</div>
                                <div class="detail-value" style="font-size: 0.95rem;">
                                    #<?php echo str_pad($row['id_registrasi'], 6, '0', STR_PAD_LEFT); ?>
                                </div>
                            </div>
                        </div>

                        <div class="history-actions">
                            <button class="btn-modern btn-primary-modern"
                                onclick="showDetail(<?php echo $row['id_registrasi']; ?>)">
                                <i class="fas fa-eye"></i>
                                Lihat Detail
                            </button>

                            <?php if ($status == 'belum-bayar'): ?>
                                <a href="../pembayaran.php?id=<?php echo $row['id_registrasi']; ?>"
                                    class="btn-modern btn-outline-modern">
                                    <i class="fas fa-credit-card"></i>
                                    Bayar Sekarang
                                </a>
                            <?php endif; ?>

                            <?php if ($status == 'approved'): ?>
                                <button class="btn-modern btn-secondary-modern"
                                    onclick="downloadTicket(<?php echo $row['id_registrasi']; ?>)">
                                    <i class="fas fa-download"></i>
                                    Download Tiket
                                </button>
                            <?php endif; ?>

                            <?php if ($status != 'belum-bayar'): ?>
                                <button class="btn-modern btn-danger-modern"
                                    onclick="confirmDelete(<?php echo $row['id_registrasi']; ?>, '<?php echo htmlspecialchars($row['nama_gunung']); ?>')">
                                    <i class="fas fa-trash"></i>
                                    Hapus
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <h3 class="empty-title">Belum Ada Riwayat Transaksi</h3>
                    <p class="empty-text">Anda belum melakukan pendaftaran pendakian. Mulai petualangan Anda sekarang!</p>
                    <a href="../index.php" class="btn-modern btn-primary-modern">
                        <i class="fas fa-plus"></i>
                        Daftar Pendakian
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-danger">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">Apakah Anda yakin ingin menghapus riwayat
                        pendakian ini?</p>
                    <div style="background: var(--light-gray); padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <strong>Gunung:</strong> <span id="deleteMountainName"></span>
                    </div>
                    <p style="color: var(--danger); font-weight: 600;">
                        <i class="fas fa-info-circle"></i> Data yang dihapus tidak dapat dikembalikan!
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="delete_id" id="deleteId">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-2"><strong>MountHub</strong> - Platform Booking Pendakian Terpercaya</p>
            <p>&copy; <?php echo date('Y'); ?> All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
     <script>
        // Profile Dropdown Toggle
        const profileBtn = document.getElementById('profileBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.remove('show');
                }
            });

            // Prevent dropdown from closing when clicking inside
            profileDropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

    </script>
    <script>
        function filterStatus() {
            const filter = document.getElementById('statusFilter').value;
            const cards = document.querySelectorAll('.history-card');

            cards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function showDetail(id) {
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();

            // Load detail via AJAX
            fetch('ajax_detail_transaksi.php?id=' + id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('modalContent').innerHTML = data;
                })
                .catch(error => {
                    document.getElementById('modalContent').innerHTML =
                        '<div class="alert alert-danger">Gagal memuat data</div>';
                });
        }

        function downloadTicket(id) {
            window.open('download_ticket.php?id=' + id, '_blank');
        }

        function confirmDelete(id, mountainName) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteMountainName').textContent = mountainName;

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
</body>

</html>