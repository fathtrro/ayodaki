<?php
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
<html>
<head>
    <title>Detail Gunung <?php echo $gunung['nama_gunung']; ?></title>
</head>
<body>
    <h1>Detail Gunung <?php echo $gunung['nama_gunung']; ?></h1>
    
    <h2>Informasi Gunung</h2>
    <p><strong>Nama:</strong> <?php echo $gunung['nama_gunung']; ?></p>
    <p><strong>Ketinggian:</strong> <?php echo $gunung['ketinggian']; ?> mdpl</p>
    <p><strong>Lokasi:</strong> <?php echo $gunung['nama_lokasi'] . ", " . $gunung['provinsi']; ?></p>
    <p><strong>Tingkat Kesulitan:</strong> <?php echo $gunung['tingkat_kesulitan']; ?></p>
    <p><strong>Jumlah Pendaki:</strong> <?php echo $count; ?> orang</p>
    
    <h2>Deskripsi</h2>
    <p><?php echo $gunung['deskripsi']; ?></p>
    
    <h2>Syarat & Ketentuan</h2>
    <p><?php echo $gunung['syarat_ketentuan']; ?></p>
    
    <h2>Pendaftaran Pendakian</h2>
    <a href="form_pendaftaran.php?id_gunung=<?php echo $id_gunung; ?>">
        <button>Daftar Pendakian</button>
    </a>
    
    <br><br>
    <a href="index.php">Kembali ke Daftar Gunung</a>
</body>
</html>