<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Booking Pendakian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Portal Booking Pendakian</a>
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
                echo '    <img src="' . $gunung['gambar'] . '" class="card-img-top" alt="Gambar ' . htmlspecialchars($gunung['nama_gunung']) . '" style="height: 200px; object-fit: cover;">';
            } else {
                echo '    <img src="https://via.placeholder.com/400x200?text=Tidak+Ada+Gambar" class="card-img-top" alt="Tidak ada gambar">';
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
    </div>
</div>

<footer class="text-center text-muted py-3 mt-5 border-top">
    <small>&copy; <?php echo date('Y'); ?> Portal Booking Pendakian</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
