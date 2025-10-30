<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_gunung = $_SESSION['id_gunung'];
    $tanggal_pendakian = $_SESSION['tanggal_pendakian'];
    $jumlah_orang = $_SESSION['jumlah_orang'];
    $no_hp = $_SESSION['no_hp'];
    
    // Simpan data ke tabel registrasi
    mysqli_query($conn, "INSERT INTO registrasi (id_gunung, tanggal_pendakian, no_hp) 
                        VALUES ($id_gunung, '$tanggal_pendakian', '$no_hp')");
    $id_registrasi = mysqli_insert_id($conn);
    
    // Simpan data pendaki
    for ($i = 0; $i < $jumlah_orang; $i++) {
        $nama = $_POST['nama'][$i];
        $email = $_POST['email'][$i];
        $alamat = $_POST['alamat'][$i];
        
        mysqli_query($conn, "INSERT INTO pendaki (id_registrasi, nama, email, alamat) 
                            VALUES ($id_registrasi, '$nama', '$email', '$alamat')");
    }
    
    $_SESSION['id_registrasi'] = $id_registrasi;
    header("Location: pembayaran.php");
    exit;
}

 $jumlah_orang = $_SESSION['jumlah_orang'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Pendaftaran Pendakian - Detail</title>
</head>
<body>
    <h1>Data Pendaki</h1>
    <p>Jumlah Orang: <?php echo $jumlah_orang; ?></p>
    
    <form method="post">
        <?php for ($i = 1; $i <= $jumlah_orang; $i++): ?>
            <h3>Pendaki <?php echo $i; ?></h3>
            
            <label>Nama Lengkap:</label>
            <input type="text" name="nama[]" required><br><br>
            
            <label>Email:</label>
            <input type="email" name="email[]" required><br><br>
            
            <label>Alamat:</label>
            <textarea name="alamat[]" required></textarea><br><br>
            
            <hr>
        <?php endfor; ?>
        
        <input type="submit" value="Lanjut ke Pembayaran">
    </form>
</body>
</html>