<?php
session_start();
include 'config.php';

// Pagination settings
$items_per_page = 6;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get filter parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$difficulty = isset($_GET['difficulty']) ? mysqli_real_escape_string($conn, $_GET['difficulty']) : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

// Build WHERE clause
$where_conditions = [];
if (!empty($search)) {
    $where_conditions[] = "(g.nama_gunung LIKE '%$search%' OR l.nama_lokasi LIKE '%$search%')";
}
if ($difficulty !== 'all') {
    $difficulty_map = [
        'mudah' => "g.tingkat_kesulitan LIKE '%mudah%' OR g.tingkat_kesulitan LIKE '%easy%'",
        'sedang' => "g.tingkat_kesulitan LIKE '%sedang%' OR g.tingkat_kesulitan LIKE '%medium%'",
        'sulit' => "g.tingkat_kesulitan LIKE '%sulit%' OR g.tingkat_kesulitan LIKE '%hard%' OR g.tingkat_kesulitan LIKE '%ekstrem%'"
    ];
    if (isset($difficulty_map[$difficulty])) {
        $where_conditions[] = "(" . $difficulty_map[$difficulty] . ")";
    }
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Build ORDER BY clause
$order_by = "ORDER BY g.id_gunung DESC";
switch ($sort) {
    case 'name-asc':
        $order_by = "ORDER BY g.nama_gunung ASC";
        break;
    case 'name-desc':
        $order_by = "ORDER BY g.nama_gunung DESC";
        break;
    case 'height-asc':
        $order_by = "ORDER BY g.ketinggian ASC";
        break;
    case 'height-desc':
        $order_by = "ORDER BY g.ketinggian DESC";
        break;
}

// Count total records
$count_query = "SELECT COUNT(*) as total FROM gunung g JOIN lokasi l ON g.id_lokasi = l.id_lokasi $where_clause";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $items_per_page);

// Fetch mountains with pagination
$query = "SELECT g.*, l.nama_lokasi, l.provinsi 
          FROM gunung g 
          JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
          $where_clause 
          $order_by 
          LIMIT $items_per_page OFFSET $offset";
$result = mysqli_query($conn, $query);

// Get statistics
$total_mountains = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM gunung"));
$total_climbers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaki"))['total'];
$total_locations = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT id_lokasi FROM gunung"));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Booking Pendakian - AyoDaki</title>
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
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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
            box-shadow: var(--shadow-sm);
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

        /* Hero */
        .hero {
            position: relative;
            height: 50vh;
            overflow: hidden;
        }

        .bg-video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            transform: translate(-50%, -50%);
            object-fit: cover;
            opacity: 0.85;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            padding-top: 90px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.25rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* Stats */
        .stats {
            margin-top: -3rem;
            margin-bottom: 4rem;
            position: relative;
            z-index: 2;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
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

        .stat-icon.green { background: var(--primary-light); color: var(--primary); }
        .stat-icon.purple { background: #e0e7ff; color: var(--secondary); }
        .stat-icon.orange { background: #fed7aa; color: #ea580c; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Search Section */
        .search-section {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: var(--shadow-sm);
        }

        .search-wrapper {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s;
            background: #fafafa;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .filter-chips {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .chip {
            padding: 0.75rem 1.25rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: var(--white);
            color: var(--gray);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .chip:hover { border-color: var(--primary); color: var(--primary); }
        .chip.active { background: var(--primary); color: white; border-color: var(--primary); }

        .sort-select {
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sort-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        /* Mountain Card */
        .mountain-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .mountain-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .card-image {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .mountain-card:hover .card-image img {
            transform: scale(1.08);
        }

        .image-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: var(--shadow-md);
        }

        .card-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .card-title a {
            color: var(--dark);
            text-decoration: none;
            transition: color 0.2s;
        }

        .card-title a:hover {
            color: var(--primary);
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .info-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--light-gray);
            color: var(--gray);
        }

        .difficulty-tag {
            display: inline-flex;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .difficulty-easy { background: var(--primary-light); color: #065f46; }
        .difficulty-medium { background: #fef3c7; color: #92400e; }
        .difficulty-hard { background: #fee2e2; color: #991b1b; }

        .climber-stat {
            background: linear-gradient(135deg, #f0fdf4 0%, var(--primary-light) 100%);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            margin: auto 0 1rem 0;
        }

        .climber-number {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .btn-modern {
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary-modern {
            background: var(--primary);
            color: white;
        }

        .btn-primary-modern:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
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

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 3rem;
            padding: 2rem 0;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .page-item {
            display: inline-block;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 44px;
            padding: 0.5rem 1rem;
            background: var(--white);
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            color: var(--gray);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .page-link:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination-info {
            color: var(--gray);
            font-weight: 500;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 5rem 2rem;
        }

        .no-results-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 2rem;
            margin-top: 5rem;
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .stats { margin-top: -2rem; }
            .pagination-wrapper { flex-direction: column; }
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

    <!-- Hero -->
    <div class="hero">
        <video class="bg-video" autoplay muted loop playsinline>
            <source src="/public/assets/videoplayback.mp4" type="video/mp4">
        </video>
        <div class="hero-content">
            <div class="container">
                <h1>Jelajahi Indonesia</h1>
                <p>Booking pendakian gunung favoritmu dengan mudah dan cepat</p>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats -->
        <div class="stats">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fas fa-mountain"></i></div>
                        <div class="stat-value"><?php echo $total_mountains; ?></div>
                        <div class="stat-label">Gunung Tersedia</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                        <div class="stat-value"><?php echo $total_climbers; ?></div>
                        <div class="stat-label">Total Pendaki</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="stat-value"><?php echo $total_locations; ?></div>
                        <div class="stat-label">Lokasi</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <form method="GET" action="" id="filterForm">
            <div class="search-section">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="search-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" class="search-input" 
                                   placeholder="Cari gunung atau lokasi..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="filter-chips">
                            <button type="button" class="chip <?php echo $difficulty === 'all' ? 'active' : ''; ?>" 
                                    onclick="setFilter('all')">Semua</button>
                            <button type="button" class="chip <?php echo $difficulty === 'mudah' ? 'active' : ''; ?>" 
                                    onclick="setFilter('mudah')">Mudah</button>
                            <button type="button" class="chip <?php echo $difficulty === 'sedang' ? 'active' : ''; ?>" 
                                    onclick="setFilter('sedang')">Sedang</button>
                            <button type="button" class="chip <?php echo $difficulty === 'sulit' ? 'active' : ''; ?>" 
                                    onclick="setFilter('sulit')">Sulit</button>
                        </div>
                        <input type="hidden" name="difficulty" id="difficultyInput" value="<?php echo $difficulty; ?>">
                    </div>
                    <div class="col-lg-3">
                        <select name="sort" class="sort-select w-100" onchange="this.form.submit()">
                            <option value="default" <?php echo $sort === 'default' ? 'selected' : ''; ?>>Urutkan</option>
                            <option value="name-asc" <?php echo $sort === 'name-asc' ? 'selected' : ''; ?>>A → Z</option>
                            <option value="name-desc" <?php echo $sort === 'name-desc' ? 'selected' : ''; ?>>Z → A</option>
                            <option value="height-asc" <?php echo $sort === 'height-asc' ? 'selected' : ''; ?>>Terendah</option>
                            <option value="height-desc" <?php echo $sort === 'height-desc' ? 'selected' : ''; ?>>Tertinggi</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>

        <!-- Mountain Grid -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="row g-4">
                <?php while ($gunung = mysqli_fetch_assoc($result)):
                    $count_query = "SELECT COUNT(*) as total FROM pendaki p 
                                    JOIN registrasi r ON p.id_registrasi = r.id_registrasi 
                                    WHERE r.id_gunung = " . $gunung['id_gunung'];
                    $count = mysqli_fetch_assoc(mysqli_query($conn, $count_query))['total'];

                    $difficulty_class = 'difficulty-easy';
                    $difficulty_lower = strtolower($gunung['tingkat_kesulitan']);
                    if (strpos($difficulty_lower, 'sedang') !== false) {
                        $difficulty_class = 'difficulty-medium';
                    } elseif (strpos($difficulty_lower, 'sulit') !== false || strpos($difficulty_lower, 'ekstrem') !== false) {
                        $difficulty_class = 'difficulty-hard';
                    }
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="mountain-card">
                        <div class="card-image">
                            <img src="<?php echo !empty($gunung['gambar']) ? htmlspecialchars($gunung['gambar']) : 'https://www.alshameltechno.com/wp-content/themes/alshameltechno/images/sample.webp'; ?>" 
                                 alt="<?php echo htmlspecialchars($gunung['nama_gunung']); ?>">
                            <div class="image-badge">
                                <i class="fas fa-mountain"></i> <?php echo number_format($gunung['ketinggian']); ?> mdpl
                            </div>
                        </div>
                        <div class="card-content">
                            <h3 class="card-title">
                                <a href="detail_gunung.php?id=<?php echo $gunung['id_gunung']; ?>">
                                    <?php echo htmlspecialchars($gunung['nama_gunung']); ?>
                                </a>
                            </h3>
                            <div class="info-row">
                                <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="info-text"><?php echo htmlspecialchars($gunung['nama_lokasi']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-icon"><i class="fas fa-signal"></i></div>
                                <span class="difficulty-tag <?php echo $difficulty_class; ?>">
                                    <?php echo htmlspecialchars($gunung['tingkat_kesulitan']); ?>
                                </span>
                            </div>
                            <div class="climber-stat">
                                <div class="climber-number">
                                    <i class="fas fa-users"></i> <?php echo $count; ?>
                                </div>
                                <div class="climber-label">Pendaki Terdaftar</div>
                            </div>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                                <a href="form_pendaftaran.php?id_gunung=<?php echo $gunung['id_gunung']; ?>" 
                                   class="btn-modern btn-primary-modern w-100">
                                    <i class="fas fa-hiking"></i> Daftar Sekarang
                                </a>
                            <?php else: ?>
                                <a href="detail_gunung.php?id=<?php echo $gunung['id_gunung']; ?>" 
                                   class="btn-modern btn-outline-modern w-100">
                                    <i class="fas fa-arrow-right"></i> Lihat Detail
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Halaman <?php echo $current_page; ?> dari <?php echo $total_pages; ?>
                    (<?php echo $total_records; ?> gunung)
                </div>
                <ul class="pagination">
                    <!-- Previous -->
                    <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&difficulty=<?php echo $difficulty; ?>&sort=<?php echo $sort; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    if ($start_page > 1):
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1&search=<?php echo urlencode($search); ?>&difficulty=<?php echo $difficulty; ?>&sort=<?php echo $sort; ?>">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&difficulty=<?php echo $difficulty; ?>&sort=<?php echo $sort; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&difficulty=<?php echo $difficulty; ?>&sort=<?php echo $sort; ?>">
                                <?php echo $total_pages; ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Next -->
                    <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&difficulty=<?php echo $difficulty; ?>&sort=<?php echo $sort; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="no-results">
                <div class="no-results-icon">🔍</div>
                <h3>Tidak ada hasil ditemukan</h3>
                <p>Coba ubah kata kunci atau filter pencarian Anda</p>
                <a href="index.php" class="btn-modern btn-primary-modern mt-3">
                    <i class="fas fa-redo"></i> Reset Pencarian
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-2"><strong>AyoDaki</strong> - Platform Booking Pendakian Terpercaya</p>
            <p>&copy; <?php echo date('Y'); ?> All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setFilter(difficulty) {
            document.getElementById('difficultyInput').value = difficulty;
            document.getElementById('filterForm').submit();
        }

        // Auto-submit search with debounce
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        }

        // Smooth scroll animation
        document.querySelectorAll('.mountain-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease-out';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });
    </script>
</body>

</html>