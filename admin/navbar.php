<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
    }

    .sidebar {
        background: linear-gradient(180deg, #1e4620 0%, #0d2818 100%);
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
        position: relative;
        min-height: 100vh;
    }

    .sidebar::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.5;
        pointer-events: none;
    }

    .nav-link {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 10px;
        margin: 4px 10px;
        position: relative;
        overflow: hidden;
        border-left: 3px solid transparent;
        color: rgba(255, 255, 255, 0.8) !important;
        padding: 0.65rem 1rem !important;
        font-size: 0.85rem;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .nav-link:hover::before {
        left: 100%;
    }

    .nav-link:hover {
        background: rgba(34, 197, 94, 0.15) !important;
        transform: translateX(6px);
        border-left-color: #22c55e;
        box-shadow: 0 3px 10px rgba(34, 197, 94, 0.2);
        color: white !important;
    }

    .nav-link.active {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
        transform: translateX(6px);
        border-left-color: #fbbf24;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
        color: white !important;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        background: #fbbf24;
        border-radius: 50%;
        box-shadow: 0 0 8px #fbbf24;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }

        50% {
            opacity: 0.5;
            transform: translateY(-50%) scale(1.2);
        }
    }

    .nav-link i {
        font-size: 0.95rem;
        transition: transform 0.3s ease;
        width: 20px;
        text-align: center;
    }

    .nav-link:hover i {
        transform: scale(1.15) rotate(5deg);
    }

    .nav-link.active i {
        animation: bounce 0.6s ease;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-4px);
        }
    }

    .sidebar .text-center {
        background: rgba(0, 0, 0, 0.2);
        padding: 1rem;
        border-radius: 12px;
        margin: 0 10px 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .sidebar .text-center img {
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        padding: 6px;
        border-radius: 50%;
    }

    .sidebar .text-center img:hover {
        transform: scale(1.08) rotate(5deg);
        border-color: #22c55e;
        box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
    }

    .sidebar .text-center h4 {
        margin-top: 0.75rem;
        margin-bottom: 0.25rem;
        font-weight: 700;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        letter-spacing: 0.3px;
        color: white;
        font-size: 1.1rem;
    }

    .sidebar .text-center p {
        font-size: 0.75rem;
        opacity: 0.9;
        font-weight: 400;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 0;
    }

    .mountain-bg {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 120px;
        background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%2322c55e" fill-opacity="0.15" d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,138.7C672,128,768,128,864,138.7C960,149,1056,171,1152,170.7C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path><path fill="%2316a34a" fill-opacity="0.1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        opacity: 0.6;
        pointer-events: none;
    }

    .nav-link span {
        font-weight: 500;
        letter-spacing: 0.2px;
    }

    .sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(34, 197, 94, 0.5);
        border-radius: 10px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(34, 197, 94, 0.8);
    }

    .nav-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        margin: 0.75rem 1.25rem;
    }
</style>

<!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar collapse text-white">
    <div class="position-sticky pt-3 pb-20">
        <div class="text-center mb-3 relative">
           
            <h4>Admin Panel</h4>
            <p>Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?></p>
        </div>

        <ul class="nav flex-column mt-3">
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>"
                    href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_gunung.php') ? 'active' : ''; ?>"
                    href="manage_gunung.php">
                    <i class="fas fa-mountain me-2"></i>
                    <span>Kelola Gunung</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center <?php echo (basename($_SERVER['PHP_SELF']) == 'approve_registrasi.php') ? 'active' : ''; ?>"
                    href="approve_registrasi.php">
                    <i class="fas fa-check-circle me-2"></i>
                    <span>Approve Registrasi</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_pendaki.php') ? 'active' : ''; ?>"
                    href="manage_pendaki.php">
                    <i class="fas fa-users me-2"></i>
                    <span>Kelola Pendaki</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center <?php echo (basename($_SERVER['PHP_SELF']) == 'tracking_pendakian.php') ? 'active' : ''; ?>"
                    href="tracking_pendakian.php">
                    <i class="fas fa-route me-2"></i>
                    <span>Tracking Pendakian</span>
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center <?php echo (basename($_SERVER['PHP_SELF']) == 'update_status_selesai.php') ? 'active' : ''; ?>"
                    href="update_status_selesai.php">
                    <i class="fas fa-flag-checkered me-2"></i>
                    <span>Update Status Selesai</span>
                </a>
            </li>

            <div class="nav-divider"></div>

            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="../logout.php"
                    onclick="return confirm('Yakin ingin logout?')">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="mountain-bg"></div>
</nav>