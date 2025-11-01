<?php
session_start();
include 'config.php';
 $id_gunung = $_GET['id'];

 $result = mysqli_query($conn, "SELECT g.*, l.nama_lokasi, l.provinsi 
                              FROM gunung g 
                              JOIN lokasi l ON g.id_lokasi = l.id_lokasi 
                              WHERE g.id_gunung = $id_gunung");
 $gunung = mysqli_fetch_assoc($result);

// Hitung jumlah pendaki
 $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaki p 
                                    JOIN registrasi r ON p.id_registrasi = r.id_registrasi 
                                    WHERE r.id_gunung = $id_gunung");
 $count = mysqli_fetch_assoc($count_result)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Gunung <?php echo $gunung['nama_gunung']; ?> - Portal Booking Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .gunung-image {
            max-height: 400px;
            object-fit: cover;
            width: 100%;
            border-radius: 10px;
        }
        .info-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .section-title {
            border-bottom: 2px solid #198754;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #198754;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Portal Booking Pendakian</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo ($_SESSION['role'] == 'admin') ? 'admin/dashboard.php' : 'user/dashboard.php'; ?>">
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Daftar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- Gambar Gunung -->
                <?php if (!empty($gunung['gambar']) && file_exists($gunung['gambar'])): ?>
                    <img src="<?php echo $gunung['gambar']; ?>" class="gunung-image mb-4" alt="Gambar <?php echo htmlspecialchars($gunung['nama_gunung']); ?>">
                <?php else: ?>
                    <img src="https://via.placeholder.com/800x400?text=Tidak+Ada+Gambar" class="gunung-image mb-4" alt="Tidak ada gambar">
                <?php endif; ?>
                
                <!-- Informasi Gunung -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h2 class="section-title">Informasi Gunung</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nama Gunung:</strong> <?php echo htmlspecialchars($gunung['nama_gunung']); ?></p>
                                <p><strong>Ketinggian:</strong> <?php echo htmlspecialchars($gunung['ketinggian']); ?> mdpl</p>
                                <p><strong>Lokasi:</strong> <?php echo htmlspecialchars($gunung['nama_lokasi'] . ", " . $gunung['provinsi']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tingkat Kesulitan:</strong> 
                                    <?php 
                                    $badge_class = '';
                                    if ($gunung['tingkat_kesulitan'] == 'Mudah') {
                                        $badge_class = 'bg-success';
                                    } elseif ($gunung['tingkat_kesulitan'] == 'Sedang') {
                                        $badge_class = 'bg-warning';
                                    } else {
                                        $badge_class = 'bg-danger';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($gunung['tingkat_kesulitan']); ?></span>
                                </p>
                                <p><strong>Jumlah Pendaki:</strong> <?php echo $count; ?> orang</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Deskripsi -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h2 class="section-title">Deskripsi</h2>
                        <p><?php echo nl2br(htmlspecialchars($gunung['deskripsi'])); ?></p>
                    </div>
                </div>
                
                <!-- Syarat & Ketentuan -->
                <div class="card info-card mb-4">
                    <div class="card-body">
                        <h2 class="section-title">Syarat & Ketentuan</h2>
                        <p><?php echo nl2br(htmlspecialchars($gunung['syarat_ketentuan'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Card Pendaftaran -->
                <div class="card info-card mb-4 sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h2 class="section-title">Pendaftaran Pendakian</h2>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user'): ?>
                            <a href="form_pendaftaran.php?id_gunung=<?php echo $id_gunung; ?>" class="btn btn-success w-100 mb-3">
                                <i class="bi bi-clipboard-plus"></i> Daftar Pendakian
                            </a>
                        <?php elseif (!isset($_SESSION['user_id'])): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Silahkan login terlebih dahulu untuk mendaftar pendakian.
                            </div>
                            <a href="login.php" class="btn btn-success w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                            <p class="text-center">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Halaman ini hanya untuk user. Admin tidak dapat mendaftar pendakian.
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <h5>Informasi Penting:</h5>
                        <ul class="small">
                            <li>Pastikan kondisi fisik prima sebelum mendaki</li>
                            <li>Bawa perlengkapan standar pendakian</li>
                            <li>Ikuti aturan dan jaga kebersihan alam</li>
                            <li>Siapkan identitas diri yang sah</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Gunung
                </a>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted py-3 mt-5 border-top">
        <small>&copy; <?php echo date('Y'); ?> Portal Booking Pendakian</small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>