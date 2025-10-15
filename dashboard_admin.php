<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login_admin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Portal Pendakian</a>
        <div>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="text-center mb-4">
        <h2 class="fw-semibold text-success">Dashboard Admin</h2>
        <p class="lead text-muted">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>!</p>
        <p>Kelola data gunung, pendaki, dan pendaftaran dari sini.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Data Gunung</h5>
                    <p class="card-text text-muted small">Kelola informasi gunung dan lokasi.</p>
                    <a href="data_gunung.php" class="btn btn-success btn-sm w-100">Lihat</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Data Pendaki</h5>
                    <p class="card-text text-muted small">Lihat dan kelola data pendaki terdaftar.</p>
                    <a href="data_pendaki.php" class="btn btn-success btn-sm w-100">Lihat</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">Pendaftaran</h5>
                    <p class="card-text text-muted small">Pantau dan konfirmasi pendaftaran pendakian.</p>
                    <a href="data_registrasi.php" class="btn btn-success btn-sm w-100">Lihat</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center text-muted py-3 border-top">
    <small>&copy; <?php echo date('Y'); ?> Portal Booking Pendakian</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
