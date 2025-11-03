<?php
session_start();
include 'config.php';

// Validate and sanitize input
$id_gunung = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_gunung <= 0) {
    header('Location: index.php');
    exit;
}

// Fetch mountain details
$query = "SELECT g.*, l.nama_lokasi, l.provinsi 
          FROM gunung g 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
          WHERE g.id_gunung = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id_gunung);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header('Location: index.php');
    exit;
}

$gunung = mysqli_fetch_assoc($result);

// Count climbers
$count_query = "SELECT COUNT(*) as total 
                FROM pendaki p 
                JOIN registrasi r ON p.id_registrasi = r.id_registrasi 
                WHERE r.id_gunung = ?";
$count_stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($count_stmt, "i", $id_gunung);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count = mysqli_fetch_assoc($count_result)['total'];

// Determine difficulty class
function getDifficultyClass($difficulty)
{
    $difficulty_lower = strtolower($difficulty);
    if (strpos($difficulty_lower, 'sedang') !== false || strpos($difficulty_lower, 'medium') !== false) {
        return 'difficulty-medium';
    } elseif (strpos($difficulty_lower, 'sulit') !== false || strpos($difficulty_lower, 'hard') !== false || strpos($difficulty_lower, 'ekstrem') !== false) {
        return 'difficulty-hard';
    }
    return 'difficulty-easy';
}

$difficulty_class = getDifficultyClass($gunung['tingkat_kesulitan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?php echo htmlspecialchars($gunung['nama_gunung']); ?> - AyoDaki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #d1fae5;
            --secondary: #6366f1;
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f1f5f9;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fafafa;
            color: var(--dark);
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background: var(--white) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1.2rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark) !important;
        }

        .navbar-brand i {
            color: var(--primary);
            margin-right: 0.5rem;
        }

        .nav-link {
            color: var(--gray) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            background: var(--light-gray);
        }

        /* Hero Section */
        .detail-hero {
            position: relative;
            height: 460px;
            overflow: hidden;
            margin-bottom: 3rem;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.2) 0%, rgba(15, 23, 42, 0.7) 100%);
        }

        .hero-content {
            position: absolute;
            bottom: 2rem;
            left: 0;
            right: 0;
            color: white;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .hero-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .hero-meta-item i {
            color: var(--primary);
        }

        /* Content */
        .content-wrapper {
            padding: 0 0 4rem;
        }

        .info-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }

        .info-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-gray);
        }

        .section-title i {
            color: var(--primary);
        }

        /* Quick Info Grid */
        .quick-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.25rem;
        }

        .quick-info-item {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s;
        }

        .quick-info-item:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        .quick-info-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .quick-info-icon.green { background: var(--primary-light); color: var(--primary); }
        .quick-info-icon.blue { background: #dbeafe; color: #2563eb; }
        .quick-info-icon.purple { background: #e0e7ff; color: var(--secondary); }
        .quick-info-icon.orange { background: #fed7aa; color: #ea580c; }

        .quick-info-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .quick-info-value {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--dark);
        }

        .difficulty-badge {
            display: inline-flex;
            padding: 0.4rem 0.9rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .difficulty-easy { background: var(--primary-light); color: #065f46; }
        .difficulty-medium { background: #fef3c7; color: #92400e; }
        .difficulty-hard { background: #fee2e2; color: #991b1b; }

        .description-text {
            font-size: 1rem;
            line-height: 1.8;
            color: var(--gray);
            white-space: pre-line;
        }

        /* Sidebar */
        .sidebar-wrapper {
            position: sticky;
            top: 100px;
        }

        .registration-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 16px;
            padding: 2rem;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
            margin-bottom: 1.5rem;
        }

        .registration-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .climber-stats {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .climber-number {
            font-size: 2.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .climber-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .btn-modern {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            text-decoration: none;
        }

        .btn-white {
            background: white;
            color: var(--primary);
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(255, 255, 255, 0.3);
            color: var(--primary);
        }

        .btn-outline-white {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-white:hover {
            background: white;
            color: var(--primary);
        }

        .alert-modern {
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .info-list {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.25rem;
        }

        .info-list-title {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            padding: 0.6rem 0;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.88rem;
        }

        .info-list li i {
            color: var(--primary);
            background: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            margin-top: 0.15rem;
            flex-shrink: 0;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            background: var(--white);
            color: var(--gray);
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            border: 2px solid #e2e8f0;
            margin-top: 1rem;
        }

        .back-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-4px);
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 2rem;
            margin-top: 5rem;
        }

        @media (max-width: 991px) {
            .sidebar-wrapper {
                position: relative;
                top: 0;
                margin-top: 2rem;
            }
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .hero-meta { gap: 1rem; }
            .quick-info-grid { grid-template-columns: 1fr; }
            .detail-hero { height: 350px; }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
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
                                    <li class="nav-item">
                                        <a class="nav-link" href="user/riwayat.php">
                                            <i class="fas fa-receipt"></i>Cek Pembayaran
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="logout.php">
                                            <i class="fas fa-sign-out-alt"></i>Logout
                                        </a>
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

    <!-- Hero Section -->
    <div class="detail-hero">
        <?php
        $image_src = !empty($gunung['gambar'])
            ? htmlspecialchars($gunung['gambar'])
            : 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&h=1080&fit=crop';
        ?>
        <img src="<?php echo $image_src; ?>" 
             class="hero-image"
             alt="<?php echo htmlspecialchars($gunung['nama_gunung']); ?>"
             onerror="this.src='https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&h=1080&fit=crop'">
        
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <div class="container">
                <div class="hero-badge">
                    <i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars($gunung['nama_lokasi'] . ", " . $gunung['provinsi']); ?>
                </div>
                <h1 class="hero-title"><?php echo htmlspecialchars($gunung['nama_gunung']); ?></h1>
                <div class="hero-meta">
                    <div class="hero-meta-item">
                        <i class="fas fa-mountain"></i>
                        <?php echo number_format($gunung['ketinggian']); ?> mdpl
                    </div>
                    <div class="hero-meta-item">
                        <i class="fas fa-users"></i>
                        <?php echo $count; ?> Pendaki
                    </div>
                    <div class="hero-meta-item">
                        <i class="fas fa-signal"></i>
                        <?php echo htmlspecialchars($gunung['tingkat_kesulitan']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="content-wrapper">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- Quick Info -->
                    <div class="info-card">
                        <h2 class="section-title">
                            <i class="fas fa-info-circle"></i>
                            Informasi Gunung
                        </h2>
                        <div class="quick-info-grid">
                            <div class="quick-info-item">
                                <div class="quick-info-icon green">
                                    <i class="fas fa-mountain"></i>
                                </div>
                                <div class="quick-info-content">
                                    <div class="quick-info-label">Nama Gunung</div>
                                    <div class="quick-info-value">
                                        <?php echo htmlspecialchars($gunung['nama_gunung']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="quick-info-item">
                                <div class="quick-info-icon blue">
                                    <i class="fas fa-arrows-alt-v"></i>
                                </div>
                                <div class="quick-info-content">
                                    <div class="quick-info-label">Ketinggian</div>
                                    <div class="quick-info-value">
                                        <?php echo number_format($gunung['ketinggian']); ?> mdpl
                                    </div>
                                </div>
                            </div>
                            
                            <div class="quick-info-item">
                                <div class="quick-info-icon purple">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="quick-info-content">
                                    <div class="quick-info-label">Lokasi</div>
                                    <div class="quick-info-value">
                                        <?php echo htmlspecialchars($gunung['nama_lokasi']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="quick-info-item">
                                <div class="quick-info-icon orange">
                                    <i class="fas fa-signal"></i>
                                </div>
                                <div class="quick-info-content">
                                    <div class="quick-info-label">Kesulitan</div>
                                    <div class="quick-info-value">
                                        <span class="difficulty-badge <?php echo $difficulty_class; ?>">
                                            <?php echo htmlspecialchars($gunung['tingkat_kesulitan']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="info-card">
                        <h2 class="section-title">
                            <i class="fas fa-align-left"></i>
                            Deskripsi
                        </h2>
                        <div class="description-text">
                            <?php echo htmlspecialchars($gunung['deskripsi']); ?>
                        </div>
                    </div>

                    <!-- Syarat & Ketentuan -->
                    <div class="info-card">
                        <h2 class="section-title">
                            <i class="fas fa-clipboard-list"></i>
                            Syarat & Ketentuan
                        </h2>
                        <div class="description-text">
                            <?php echo htmlspecialchars($gunung['syarat_ketentuan']); ?>
                        </div>
                    </div>

                    <a href="index.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Daftar Gunung
                    </a>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar-wrapper">
                        <div class="registration-card">
                            <h3 class="registration-title">
                                <i class="fas fa-hiking"></i> Pendaftaran
                            </h3>

                            <div class="climber-stats">
                                <div class="climber-number">
                                    <i class="fas fa-users"></i> <?php echo $count; ?>
                                </div>
                                <div class="climber-label">Pendaki Terdaftar</div>
                            </div>

                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                                    <a href="form_pendaftaran.php?id_gunung=<?php echo $id_gunung; ?>"
                                        class="btn-modern btn-white">
                                        <i class="fas fa-clipboard-plus"></i>
                                        Daftar Pendakian
                                    </a>
                            <?php elseif (!isset($_SESSION['user_id'])): ?>
                                    <div class="alert-modern">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Silahkan login terlebih dahulu untuk mendaftar pendakian
                                    </div>
                                    <a href="login.php" class="btn-modern btn-outline-white mb-2">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Login
                                    </a>
                                    <p class="text-center mb-0" style="font-size: 0.9rem;">
                                        Belum punya akun? 
                                        <a href="register.php" style="color: white; text-decoration: underline; font-weight: 600;">
                                            Daftar sekarang
                                        </a>
                                    </p>
                            <?php else: ?>
                                    <div class="alert-modern">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Halaman ini hanya untuk user. Admin tidak dapat mendaftar pendakian.
                                    </div>
                            <?php endif; ?>

                            <div class="info-list">
                                <div class="info-list-title">
                                    <i class="fas fa-lightbulb"></i> Informasi Penting
                                </div>
                                <ul>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        <span>Pastikan kondisi fisik prima sebelum mendaki</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        <span>Bawa perlengkapan standar pendakian</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        <span>Ikuti aturan dan jaga kebersihan alam</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-check"></i>
                                        <span>Siapkan identitas diri yang sah</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-2"><strong>AyoDaki</strong> - Platform Booking Pendakian Terpercaya</p>
            <p>&copy; <?php echo date('Y'); ?> All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>