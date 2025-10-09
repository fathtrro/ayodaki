<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Portal Booking Pendakian</title>
</head>
<body>
    <h1>Portal Booking Pendakian</h1>
    <h2>Daftar Gunung</h2>
    
  <?php
    $result = mysqli_query($conn, "SELECT g.*, l.nama_lokasi 
                                  FROM gunung g 
                                  JOIN lokasi l ON g.id_lokasi = l.id_lokasi");
    
    while ($gunung = mysqli_fetch_assoc($result)) {
        // Hitung jumlah pendaki
        $count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaki p 
                                            JOIN registrasi r ON p.id_registrasi = r.id_registrasi 
                                            WHERE r.id_gunung = " . $gunung['id_gunung']);
        $count = mysqli_fetch_assoc($count_result)['total'];
        
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
        echo "<h3><a href='detail_gunung.php?id=" . $gunung['id_gunung'] . "'>" . $gunung['nama_gunung'] . "</a></h3>";
        echo "<p>Lokasi: " . $gunung['nama_lokasi'] . "</p>";
        echo "<p>Ketinggian: " . $gunung['ketinggian'] . " mdpl</p>";
        echo "<p>Tingkat Kesulitan: " . $gunung['tingkat_kesulitan'] . "</p>";
        echo "<p>Jumlah Pendaki: " . $count . " orang</p>";
        echo "</div>";
    }
    ?>
</body>
</html>