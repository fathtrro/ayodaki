
<?php
 include 'config.php';

 while ($gunung = mysqli_fetch_assoc($result)) {
    $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaki p 
                                        JOIN registrasi r ON p.id_registrasi = r.id_registrasi 
                                        WHERE r.id_gunung = " . $gunung['id_gunung']);
    $count = mysqli_fetch_assoc($count_result)['total'];
    
    echo '<div class="col-md-4">';
    echo '  <div class="card shadow-sm border-0 h-100">';
    
    // Gambar gunung (cek apakah tersedia)
    if (!empty($gunung['gambar']) && file_exists($gunung['gambar'])) {
        echo '    <img src="' . $gunung['gambar'] . '" class="card-img-top" alt="Gambar ' . htmlspecialchars($gunung['nama_gunung']) . '" style="height: 200px; object-fit: cover;">';
    } else {
        echo '    <img src="https://via.placeholder.com/400x200?text=Tidak+Ada+Gambar" class="card-img-top" alt="Tidak ada gambar" style="height: 200px; object-fit: cover;">';
    }

    echo '    <div class="card-body">';
    echo '        <h5 class="card-title"><a href="detail_gunung.php?id=' . $gunung['id_gunung'] . '" class="text-decoration-none text-success">' . htmlspecialchars($gunung['nama_gunung']) . '</a></h5>';
    echo '        <p class="card-text mb-1"><strong>Lokasi:</strong> ' . htmlspecialchars($gunung['nama_lokasi']) . '</p>';
    echo '        <p class="card-text mb-1"><strong>Ketinggian:</strong> ' . htmlspecialchars($gunung['ketinggian']) . ' mdpl</p>';
    echo '        <p class="card-text mb-1"><strong>Tingkat Kesulitan:</strong> ' . htmlspecialchars($gunung['tingkat_kesulitan']) . '</p>';
    echo '        <p class="card-text"><strong>Jumlah Pendaki:</strong> ' . $count . ' orang</p>';
    echo '        <a href="detail_gunung.php?id=' . $gunung['id_gunung'] . '" class="btn btn-outline-success w-100 mt-2">Lihat Detail</a>';
    echo '    </div>';
    echo '  </div>';
    echo '</div>';
}
 ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Booking Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card {
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="bg-light">

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
    <h2 class="text-center mb-4 fw-semibold">Daftar Gunung</h2>
    <div class="row g-4">
        <?php
        $result = mysqli_query($conn, "SELECT g.*, l.nama_lokasi 
                                      FROM gunung g 
                                      JOIN lokasi l ON g.id_lokasi = l.id_lokasi");
        
        while ($gunung = mysqli_fetch_assoc($result)) {
            $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaki p 
                                                JOIN registrasi r ON p.id_registrasi = r.id_registrasi 
                                                WHERE r.id_gunung = " . $gunung['id_gunung']);
            $count = mysqli_fetch_assoc($count_result)['total'];
            
            echo '<div class="col-md-4">';
            echo '  <div class="card shadow-sm border-0 h-100">';
            
            // Gambar gunung (cek apakah tersedia)
            if (!empty($gunung['gambar']) && file_exists($gunung['gambar'])) {
                echo '    <img src="' . $gunung['gambar'] . '" class="card-img-top" alt="Gambar ' . htmlspecialchars($gunung['nama_gunung']) . '">';
            } else {
                echo '    <img src="https://via.placeholder.com/400x200?text=Tidak+Ada+Gambar" class="card-img-top" alt="Tidak ada gambar">';
            }

            echo '    <div class="card-body">';
            echo '        <h5 class="card-title"><a href="detail_gunung.php?id=' . $gunung['id_gunung'] . '" class="text-decoration-none text-success">' . htmlspecialchars($gunung['nama_gunung']) . '</a></h5>';
            echo '        <p class="card-text mb-1"><strong>Lokasi:</strong> ' . htmlspecialchars($gunung['nama_lokasi']) . '</p>';
            echo '        <p class="card-text mb-1"><strong>Ketinggian:</strong> ' . htmlspecialchars($gunung['ketinggian']) . ' mdpl</p>';
            echo '        <p class="card-text mb-1"><strong>Tingkat Kesulitan:</strong> ' . htmlspecialchars($gunung['tingkat_kesulitan']) . '</p>';
            echo '        <p class="card-text"><strong>Jumlah Pendaki:</strong> ' . $count . ' orang</p>';
            
            if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user') {
                echo '        <a href="form_pendaftaran.php?id_gunung=' . $gunung['id_gunung'] . '" class="btn btn-outline-success w-100 mt-2">Daftar Pendakian</a>';
            } else {
                echo '        <a href="detail_gunung.php?id=' . $gunung['id_gunung'] . '" class="btn btn-outline-success w-100 mt-2">Lihat Detail</a>';
            }
            
            echo '    </div>';
            echo '  </div>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<footer class="text-center text-muted py-3 mt-5 border-top">
    <small>&copy; <?php echo date('Y'); ?> Portal Booking Pendakian</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>